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

use Integrated\Bundle\BlockBundle\Document\Block\Block;
use Integrated\Bundle\BlockBundle\Document\Block\TextBlock;
use Integrated\Bundle\BlockBundle\Form\Type\BlockFilterType;
use Integrated\Bundle\BlockBundle\Form\Type\LayoutChoiceType;
use Integrated\Bundle\BlockBundle\Form\Type\TextBlockType;
use Integrated\Bundle\FormTypeBundle\Form\Type\SaveCancelType;
use Integrated\Bundle\PageBundle\Document\Page\Page;
use Integrated\Common\Block\BlockInterface;
use Integrated\Common\Form\Type\MetadataType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class BlockController extends Controller
{
    /**
     * @Template
     *
     * @param Request $request
     *
     * @return array
     */
    public function indexAction(Request $request)
    {
        $pageBundleInstalled = isset($this->getParameter('kernel.bundles')['IntegratedPageBundle']);
        $data = $request->query->get('integrated_block_filter');
        $queryProvider = $this->get('integrated_block.provider.filter_query');

        $facetFilter = $this->createForm(BlockFilterType::class, null, [
            'blockIds' => $queryProvider->getBlockIds($data)
        ]);
        $facetFilter->handleRequest($request);

        $pagination = $this->getPaginator()->paginate(
            $queryProvider->getBlocksByChannelQueryBuilder($data),
            $request->query->get('page', 1),
            $request->query->get('limit', 20),
            ['defaultSortFieldName' => 'title', 'defaultSortDirection' => 'asc', 'query_type' => 'block_overview']
        );

        return [
            'blocks'  => $pagination,
            'factory' => $this->getFactory(),
            'pageBundleInstalled' => $pageBundleInstalled,
            'facetFilter' => $facetFilter->createView()
        ];
    }

    /**
     * @Template
     *
     * @param Block $block
     *
     * @return array
     */
    public function showAction(Block $block)
    {
        return [
            'block' => $block,
        ];
    }

    /**
     * @Template
     *
     * @param Request $request
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function newAction(Request $request)
    {
        $class = $request->get('class');

        $block = class_exists($class) ? new $class : null;

        if (!$block instanceof BlockInterface) {
            throw $this->createNotFoundException(sprintf('Invalid block "%s"', $class));
        }

        $form = $this->createCreateForm($block);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $dm = $this->getDocumentManager();

            $dm->persist($block);
            $dm->flush();

            if ('iframe.html' === $request->getRequestFormat()) {
                return $this->render('IntegratedBlockBundle:Block:saved.iframe.html.twig', ['id' => $block->getId()]);
            }

            $this->get('braincrafted_bootstrap.flash')->success('Block created');

            return $this->redirect($this->generateUrl('integrated_block_block_index'));
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @param Request $request
     * @param Page $parentPage
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newTextBlockAction(Request $request, Page $parentPage)
    {
        $block = new TextBlock();

        $block->setParentPage($parentPage);
        $block->setLayout('borderless.html.twig');

        $form = $this->createForm(TextBlockType::class, $block);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->getDocumentManager()->persist($block);
            $this->getDocumentManager()->flush();

            return $this->render('IntegratedBlockBundle:Block:saved.iframe.html.twig', ['id' => $block->getId()]);
        }

        return $this->render('IntegratedBlockBundle:Block:new.iframe.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Template
     *
     * @param Request $request
     * @param Block $block
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function editAction(Request $request, Block $block)
    {

        $form = $this->createEditForm($block);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->getDocumentManager()->flush();

            if ('iframe.html' === $request->getRequestFormat()) {
                return $this->render('IntegratedBlockBundle:Block:saved.iframe.html.twig', ['id' => $block->getId()]);
            }

            $this->get('braincrafted_bootstrap.flash')->success('Block updated');

            return $this->redirect($this->generateUrl('integrated_block_block_index'));
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @Template
     *
     * @param Request $request
     * @param Block $block
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Block $block)
    {
        if ($block->isLocked()) {
            throw $this->createNotFoundException(sprintf('Block "%s" is locked.', $block->getId()));
        }

        /* check if current Block not used on some page */
        $dm = $this->getDocumentManager();
        if ($this->container->has('integrated_page.form.type.page')) {
            if ($dm->getRepository('IntegratedBlockBundle:Block\Block')->isUsed($block)) {
                throw $this->createNotFoundException(sprintf('Block "%s" is used.', $block->getId()));
            }
        }

        $form = $this->createDeleteForm($block->getId());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $dm->remove($block);
            $dm->flush();

            $this->get('braincrafted_bootstrap.flash')->success('Block deleted');

            return $this->redirect($this->generateUrl('integrated_block_block_index'));
        }

        return [
            'block' => $block,
            'form'  => $form->createView(),
        ];
    }

    /**
     * @param BlockInterface $block
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function createCreateForm(BlockInterface $block)
    {
        $class = get_class($block);

        $form = $this->createForm(
            MetadataType::class,
            $block,
            [
                'method' => 'POST',
                'data_class' => $class,
            ]
        );

        $form->add('layout', LayoutChoiceType::class, [
            'type' => $block->getType(),
        ]);

        $form->add('actions', SaveCancelType::class, [
            'cancel_route' => 'integrated_block_block_index',
            'label' => 'Create',
            'button_class' => '',
        ]);

        return $form;
    }

    /**
     * @param BlockInterface $block
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function createEditForm(BlockInterface $block)
    {
        $form = $this->createForm(
            MetadataType::class,
            $block,
            [
                'method' => 'PUT',
                'data_class' => get_class($block),
            ]
        );

        $form->add('layout', LayoutChoiceType::class, [
            'type' => $block->getType(),
        ]);

        $form->add('actions', SaveCancelType::class, ['cancel_route' => 'integrated_block_block_index']);

        return $form;
    }

    /**
     * @param string $id
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function createDeleteForm($id)
    {
        $builder = $this->createFormBuilder();

        $builder->setAction($this->generateUrl('integrated_block_block_delete', ['id' => $id]));
        $builder->setMethod('DELETE');
        $builder->add('submit', 'submit', ['label' => 'Delete', 'attr' => ['class' => 'btn-danger']]);

        return $builder->getForm();
    }

    /**
     * @return \Doctrine\ODM\MongoDB\DocumentManager
     */
    protected function getDocumentManager()
    {
        return $this->get('doctrine_mongodb')->getManager();
    }

    /**
     * @return \Knp\Component\Pager\Paginator
     */
    protected function getPaginator()
    {
        return $this->get('knp_paginator');
    }

    /**
     * @return \Integrated\Common\Form\Mapping\MetadataFactoryInterface
     */
    protected function getFactory()
    {
        return $this->get('integrated_block.metadata.factory');
    }
}
