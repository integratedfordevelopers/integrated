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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\FormInterface;
use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\BlockBundle\Document\Block\Block;
use Integrated\Bundle\BlockBundle\Form\Type\BlockEditType;
use Integrated\Bundle\BlockBundle\Form\Type\BlockFilterType;
use Integrated\Bundle\UserBundle\Model\User;
use Integrated\Common\Block\BlockInterface;
use Integrated\Common\Form\Mapping\MetadataFactoryInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class BlockController extends AbstractController
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
     * @var PaginatorInterface
     */
    protected $paginator;

    /**
     * @param MetadataFactoryInterface $metadataFactory
     * @param DocumentManager          $documentManager
     * @param PaginatorInterface       $paginator
     */
    public function __construct(
        MetadataFactoryInterface $metadataFactory,
        DocumentManager $documentManager,
        PaginatorInterface $paginator
    ) {
        $this->metadataFactory = $metadataFactory;
        $this->documentManager = $documentManager;
        $this->paginator = $paginator;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $user = null;
        if (!$this->isGranted('ROLE_WEBSITE_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            $user = $this->getUser();
        }

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

        return $this->render(sprintf('@IntegratedBlock/block/index.%s.twig', $request->getRequestFormat()), [
            'blocks' => $pagination,
            'factory' => $this->metadataFactory,
            'facetFilter' => $facetFilter->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Block   $block
     *
     * @return Response
     */
    public function show(Request $request, Block $block)
    {
        if (!$this->isGranted('ROLE_WEBSITE_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $request->attributes->set('integrated_block_edit', true);

        return $this->render('@IntegratedBlock/block/show.json.twig', [
            'block' => $block,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function new(Request $request)
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
                return $this->render('@IntegratedBlock/block/saved.iframe.html.twig', ['id' => $block->getId()]);
            }

            $this->addFlash('success', 'Block created');

            return $this->redirectToRoute('integrated_block_block_index');
        }

        return $this->render(sprintf('@IntegratedBlock/block/new.%s.twig', $request->getRequestFormat()), [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function newChannelBlock(Request $request)
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
     * @return array|RedirectResponse|Response
     */
    public function edit(Request $request, Block $block)
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
                return $this->render('@IntegratedBlock/block/saved.iframe.html.twig', [
                    'id' => $block->getId(),
                ]);
            }

            $this->addFlash('success', 'Block updated');

            return $this->redirectToRoute('integrated_block_block_index');
        }

        $metadata = $this->metadataFactory->getMetadata(\get_class($block));

        return $this->render(sprintf('@IntegratedBlock/block/edit.%s.twig', $request->getRequestFormat()), [
            'form' => $form->createView(),
            'blockType' => $metadata->getType(),
        ]);
    }

    /**
     * @param Request $request
     * @param Block   $block
     *
     * @return RedirectResponse|Response
     */
    public function delete(Request $request, Block $block)
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

            $this->addFlash('success', 'Block deleted');

            return $this->redirectToRoute('integrated_block_block_index');
        }

        return $this->render('@IntegratedBlock/block/delete.html.twig', [
            'block' => $block,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param $id
     *
     * @return FormInterface
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
