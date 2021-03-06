<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\BlockBundle\Controller;

use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\BlockBundle\Document\Block\Block;
use Integrated\Bundle\BlockBundle\Form\Type\BlockEditType;
use Integrated\Bundle\BlockBundle\Form\Type\BlockFilterType;
use Integrated\Bundle\UserBundle\Model\User;
use Integrated\Common\Block\BlockInterface;
use Integrated\Common\Form\Mapping\MetadataFactoryInterface;
use Knp\Component\Pager\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class BlockController extends Controller
{
    /**
     * @var MetadataFactoryInterface
     */
    protected $metadataFactory;

    /**
     * @var DocumentManager
     */
    protected $documentManager;

    /**
     * @var Paginator
     */
    protected $paginator;

    /**
     * @param MetadataFactoryInterface $metadataFactory
     * @param DocumentManager          $documentManager
     * @param Paginator                $paginator
     */
    public function __construct(
        MetadataFactoryInterface $metadataFactory,
        DocumentManager $documentManager,
        Paginator $paginator
    ) {
        $this->metadataFactory = $metadataFactory;
        $this->documentManager = $documentManager;
        $this->paginator = $paginator;
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $user = null;
        if (!$this->isGranted('ROLE_WEBSITE_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            $user = $this->getUser();
        }

        $pageBundleInstalled = isset($this->getParameter('kernel.bundles')['IntegratedPageBundle']);
        $data = $request->get('integrated_block_filter');
        $queryProvider = $this->get('integrated_block.provider.filter_query');

        $facetFilter = $this->createForm(BlockFilterType::class, null, [
            'blockIds' => $queryProvider->getBlockIds($data, $user),
        ]);
        $facetFilter->handleRequest($request);

        $pagination = $this->paginator->paginate(
            $queryProvider->getBlocksByChannelQueryBuilder($data, $user),
            $request->query->get('page', 1),
            $request->query->get('limit', 20),
            ['defaultSortFieldName' => 'title', 'defaultSortDirection' => 'asc', 'query_type' => 'block_overview']
        );

        return $this->render(sprintf('IntegratedBlockBundle:block:index.%s.twig', $request->getRequestFormat()), [
            'blocks' => $pagination,
            'factory' => $this->metadataFactory,
            'pageBundleInstalled' => $pageBundleInstalled,
            'facetFilter' => $facetFilter->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Block   $block
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, Block $block)
    {
        if (!$this->isGranted('ROLE_WEBSITE_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $request->attributes->set('integrated_block_edit', true);

        return $this->render('IntegratedBlockBundle:block:show.json.twig', [
            'block' => $block,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        if (!$this->isGranted('ROLE_WEBSITE_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $class = $request->get('class');

        $block = class_exists($class) ? new $class() : null;

        if (!$block instanceof BlockInterface) {
            throw $this->createNotFoundException(sprintf('Invalid block "%s"', $class));
        }

        $form = $this->createForm(
            BlockEditType::class,
            $block,
            [
                'method' => 'PUT',
                'data_class' => \get_class($block),
                'type' => $block->getType(),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->documentManager->persist($block);
            $this->documentManager->flush();

            if ('iframe.html' === $request->getRequestFormat()) {
                return $this->render('IntegratedBlockBundle:block:saved.iframe.html.twig', ['id' => $block->getId()]);
            }

            $this->get('braincrafted_bootstrap.flash')->success('Block created');

            return $this->redirect($this->generateUrl('integrated_block_block_index'));
        }

        return $this->render(sprintf('IntegratedBlockBundle:block:new.%s.twig', $request->getRequestFormat()), [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newChannelBlockAction(Request $request)
    {
        $csrfToken = $request->request->get('csrf_token');

        if (!(
            ($this->isGranted('ROLE_WEBSITE_MANAGER') || $this->isGranted('ROLE_ADMIN'))
            && $this->isCsrfTokenValid('create-channel-block', $csrfToken))
        ) {
            throw $this->createAccessDeniedException();
        }

        $class = $request->request->get('class');
        $id = $request->request->get('id');
        $name = $request->request->get('name');

        $block = class_exists($class) ? new $class($id) : null;

        if (!$block instanceof Block) {
            throw $this->createNotFoundException(sprintf('Invalid block "%s"', $class));
        }

        $block->setTitle($name);
        $block->setLayout('default.html.twig');
        $this->documentManager->persist($block);
        $this->documentManager->flush();

        return new JsonResponse(['result' => 'ok']);
    }

    /**
     * @param Request $request
     * @param Block   $block
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Block $block)
    {
        if (!$this->isGranted('ROLE_WEBSITE_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            $user = $this->getUser();
            if (!$user instanceof User || !$block->allowsGroupAccess($user->getGroups())) {
                throw $this->createAccessDeniedException();
            }
        }

        $form = $this->createForm(
            BlockEditType::class,
            $block,
            [
                'method' => 'POST',
                'data_class' => \get_class($block),
                'type' => $block->getType(),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->documentManager->flush();

            if ('iframe.html' === $request->getRequestFormat()) {
                return $this->render('IntegratedBlockBundle:block:saved.iframe.html.twig', [
                    'id' => $block->getId(),
                ]);
            }

            $this->get('braincrafted_bootstrap.flash')->success('Block updated');

            return $this->redirect($this->generateUrl('integrated_block_block_index'));
        }

        $metadata = $this->metadataFactory->getMetadata(\get_class($block));

        return $this->render(sprintf('IntegratedBlockBundle:block:edit.%s.twig', $request->getRequestFormat()), [
            'form' => $form->createView(),
            'blockType' => $metadata->getType(),
        ]);
    }

    /**
     * @param Request $request
     * @param Block   $block
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request, Block $block)
    {
        if (!$this->isGranted('ROLE_WEBSITE_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        if ($block->isLocked()) {
            throw $this->createNotFoundException(sprintf('Block "%s" is locked.', $block->getId()));
        }

        /* check if current Block not used on some page */
        if ($this->container->has('integrated_page.form.type.page')) {
            if ($this->documentManager->getRepository(Block::class)->isUsed($block)) {
                throw $this->createNotFoundException(sprintf('Block "%s" is used.', $block->getId()));
            }
        }

        $form = $this->createDeleteForm($block->getId());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->documentManager->remove($block);
            $this->documentManager->flush();

            $this->get('braincrafted_bootstrap.flash')->success('Block deleted');

            return $this->redirect($this->generateUrl('integrated_block_block_index'));
        }

        return $this->render('IntegratedBlockBundle:block:delete.html.twig', [
            'block' => $block,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param $id
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function createDeleteForm($id)
    {
        $builder = $this->createFormBuilder();

        $builder->setAction($this->generateUrl('integrated_block_block_delete', ['id' => $id]));
        $builder->setMethod('DELETE');
        $builder->add('submit', SubmitType::class, ['label' => 'Delete', 'attr' => ['class' => 'btn-danger']]);

        return $builder->getForm();
    }
}
