<?php

/*
 *  This file is part of the Integrated package.
 *
 *  (c) e-Active B.V. <integrated@e-active.nl>
 *
 *   For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Controller;

use Doctrine\ODM\MongoDB\DocumentManager;

use Exception;

use Integrated\Bundle\ContentBundle\Bulk\Action\Translator\ActionTranslatorProvider;
use Integrated\Bundle\ContentBundle\Bulk\BulkHandlerInterface;
use Integrated\Bundle\ContentBundle\Form\Type\BulkActionType;
use Integrated\Bundle\ContentBundle\Form\Type\SelectionFormType;
use Integrated\Bundle\ContentBundle\Document\Bulk\BulkAction;
use Integrated\Bundle\ContentBundle\Provider\ContentProvider;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
class BulkController extends Controller
{
    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @var ContentProvider
     */
    protected $contentProvider;

    /**
     * @var BulkHandlerInterface
     */
    protected $bulkHandler;

    /**
     * @var ActionTranslatorProvider
     */
    protected $actionTranslatorProvider;

    /**
     * @param DocumentManager $dm
     * @param ContentProvider $contentProvider
     * @param BulkHandlerInterface $bulkHandler
     * @param ActionTranslatorProvider $actionTranslatorProvider
     * @param ContainerInterface $container
     */
    public function __construct(
        DocumentManager $dm,
        ContentProvider $contentProvider,
        BulkHandlerInterface $bulkHandler,
        ActionTranslatorProvider $actionTranslatorProvider,
        ContainerInterface $container
    ) {
        $this->dm = $dm;
        $this->contentProvider = $contentProvider;
        $this->bulkHandler = $bulkHandler;
        $this->actionTranslatorProvider = $actionTranslatorProvider;
        $this->container = $container;
    }

    /**
     * @Template()
     * @param Request $request
     * @return array | RedirectResponse
     */
    public function selectAction(Request $request)
    {
        // Fetch Content selection.
        $limit = 1000;

        if (!$contents = $this->contentProvider->getContentFromSolr($request, $limit + 1)) {
            return $this->redirectToRoute('integrated_content_content_index', $request->query->all());
        }

        if (!$bulkAction = $this->dm->getRepository(BulkAction::class)->findOneByIdAndNotExcuted($request->get('id'))) {
            $bulkAction = new BulkAction();
        }

        $form = $this->createForm(SelectionFormType::class, $bulkAction, ['contents' => array_slice($contents, 0, $limit)]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /* @var BulkAction $bulkAction */
            $bulkAction = $form->getData();

            if (!$bulkAction->getId()) {
                $this->dm->persist($bulkAction);
                $request->query->set('id', $bulkAction->getId());
            }

            $this->dm->flush();
            return $this->redirectToRoute('integrated_content_bulk_configure', $request->query->all());
        }

        return [
            'contents' => $contents,
            'limit' => $limit,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Template()
     * @param Request $request
     * @param BulkAction $bulkAction
     * @return array|RedirectResponse
     */
    public function configureAction(Request $request, BulkAction $bulkAction)
    {
        if ($bulkAction->getExecutedAt()) {
            $this->addFlash('danger', 'This bulk action was all ready executed and can not be configured.');
            $request->query->remove('id');
            return $this->redirectToRoute('integrated_content_content_index', $request->query->all());
        }

        dump($request);

        $form = $this->createForm(BulkActionType::class, $bulkAction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bulkAction = $form->getData();
            $this->dm->flush();

            return $this->redirectToRoute('integrated_content_bulk_confirm', $request->query->all());
        }

        return [
            'id' => $bulkAction->getId(),
            'selection' => count($bulkAction->getSelection()),
            'form' => $form->createView(),
        ];
    }

    /**
     * @Template()
     * @param Request $request
     * @param BulkAction $bulkAction
     * @return array|RedirectResponse
     */
    public function confirmAction(Request $request, BulkAction $bulkAction)
    {
        // Check if bulk action is not allready executed
        if ($bulkAction->getExecutedAt()) {
            $this->addFlash('danger', 'This bulk action was all ready executed.');
            $request->query->remove('id');
            return $this->redirectToRoute('integrated_content_content_index', $request->query->all());
        }

        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        // If form submitted try to execute all bulk action.
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->bulkHandler->execute($bulkAction->getSelection(), $bulkAction->getActions());
                $bulkAction->setExecutedAt(new \DateTime());

                $this->dm->flush();

                $request->query->remove('id');
                $this->addFlash('success', 'It seems all bulk actions were executed successfully! :)');
                return $this->redirectToRoute('integrated_content_content_index', $request->query->all());
            } catch (Exception $e) {
                $this->addFlash(
                    'danger',
                    'Whoops! It seems something went wrong during the execution of this bulk action! The following error has given: "' . $e->getMessage() . '"'
                );
            }
        }

        return [
            'id' => $bulkAction->getId(),
            'contents' => $bulkAction->getSelection(),
            'actionTranslators' => $this->actionTranslatorProvider->getTranslators($bulkAction->getActions()),
            'form' => $form->createView(),
        ];
    }
}
