<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Controller;

use Doctrine\ODM\MongoDB\DocumentManager;
use Exception;
use Integrated\Bundle\ContentBundle\Document\Bulk\BulkAction;
use Integrated\Bundle\ContentBundle\Form\Type\BulkActionConfirmType;
use Integrated\Bundle\ContentBundle\Form\Type\BulkConfigureType;
use Integrated\Bundle\ContentBundle\Form\Type\BulkSelectionType;
use Integrated\Bundle\ContentBundle\Provider\ContentProvider;
use Integrated\Common\Bulk\BulkHandlerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * @param DocumentManager      $dm
     * @param ContentProvider      $contentProvider
     * @param BulkHandlerInterface $bulkHandler
     * @param ContainerInterface   $container
     */
    public function __construct(
        DocumentManager $dm,
        ContentProvider $contentProvider,
        BulkHandlerInterface $bulkHandler,
        ContainerInterface $container
    ) {
        $this->dm = $dm;
        $this->contentProvider = $contentProvider;
        $this->bulkHandler = $bulkHandler;
        $this->container = $container;
    }

    /**
     * @param Request    $request
     * @param BulkAction $bulk
     *
     * @return RedirectResponse|Response
     */
    public function selectAction(Request $request, BulkAction $bulk = null)
    {
        // Fetch Content selection.
        $limit = 1000;

        if ($bulk) {
            $request->query->replace($bulk->getFilters());
        }

        if (!$content = $this->contentProvider->getContentFromSolr($request, $limit + 1)) {
            return $this->redirectToRoute('integrated_content_content_index', $request->query->all());
        }

        $form = $this->createForm(BulkSelectionType::class, $bulk, ['content' => \array_slice($content, 0, $limit)]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /* @var BulkAction $bulk */
            $bulk = $form->getData();
            $bulk->setFilters($request->query->all());

            if (!$bulk->getId()) {
                $this->dm->persist($bulk);
            }

            $this->dm->flush();

            return $this->redirectToRoute('integrated_content_bulk_configure', ['id' => $bulk->getId()]);
        }

        return $this->render('IntegratedContentBundle:bulk:select.html.twig', [
            'content' => $content,
            'limit' => $limit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request    $request
     * @param BulkAction $bulk
     *
     * @return RedirectResponse|Response
     */
    public function configureAction(Request $request, BulkAction $bulk)
    {
        if ($bulk->getExecutedAt()) {
            return $this->redirectToRoute('integrated_content_content_index', $bulk->getFilters());
        }

        $form = $this->createForm(BulkConfigureType::class, $bulk, ['content' => $bulk->getSelection()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->dm->flush();

            return $this->redirectToRoute('integrated_content_bulk_confirm', ['id' => $bulk->getId()]);
        }

        return $this->render('IntegratedContentBundle:bulk:configure.html.twig', [
            'id' => $bulk->getId(),
            'selection' => \count($bulk->getSelection()),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request    $request
     * @param BulkAction $bulk
     *
     * @return RedirectResponse|Response
     */
    public function confirmAction(Request $request, BulkAction $bulk)
    {
        $this->preventTimeout();

        if ($bulk->getExecutedAt()) {
            return $this->redirectToRoute('integrated_content_content_index', $bulk->getFilters());
        }

        $form = $this->createForm(BulkActionConfirmType::class, $bulk, ['content' => $bulk->getSelection()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->bulkHandler->execute($bulk->getSelection(), $bulk->getActions());
                $bulk->setExecutedAt(new \DateTime());

                $this->dm->flush();

                $this->addFlash('success', 'All bulk actions were executed successfully. Indexing operations will be executed in the background');

                return $this->redirectToRoute('integrated_content_content_index', $bulk->getFilters());
            } catch (Exception $e) {
                $this->addFlash(
                    'danger',
                    'Whoops! It seems something went wrong during the execution of this bulk action! The following error has given: "'.$e->getMessage().'"'
                );
            }
        }

        return $this->render('IntegratedContentBundle:bulk:confirm.html.twig', [
            'id' => $bulk->getId(),
            'selection' => \count($bulk->getSelection()),
            'form' => $form->createView(),
        ]);
    }

    /**
     * Try to prevent reaching the timeout on large bulk actions
     */
    private function preventTimeout()
    {
        ini_set('max_execution_time', '600');
    }
}
