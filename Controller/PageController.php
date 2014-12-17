<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Bundle\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Integrated\Bundle\WebsiteBundle\Document\Page\Page;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class PageController extends Controller
{
    /**
     * @Template
     */
    public function indexAction(Request $request)
    {
        $builder = $this->getDocumentManager()->createQueryBuilder('IntegratedWebsiteBundle:Page\Page');

        $pagination = $this->getPaginator()->paginate(
            $builder,
            $request->query->get('page', 1),
            20
        );

        return [
            'pages' => $pagination,
        ];
    }

    /**
     * @Template
     */
    public function newAction(Request $request)
    {
        $page = new Page();

        $form = $this->createCreateForm($page);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $dm = $this->getDocumentManager();

            $dm->persist($page);
            $dm->flush();

            $this->get('braincrafted_bootstrap.flash')->success('Page created');

            return $this->redirect($this->generateUrl('integrated_website_page_layout', ['id' => $page->getId()]));
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @Template
     */
    public function editAction(Request $request, Page $page)
    {
        $form = $this->createEditForm($page);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $this->getDocumentManager()->flush();

            $this->get('braincrafted_bootstrap.flash')->success('Page updated');

            return $this->redirect($this->generateUrl('integrated_website_page_layout', ['id' => $page->getId()]));
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @Template
     */
    public function deleteAction(Request $request, Page $page)
    {
        $form = $this->createDeleteForm($page->getId());
        $form->handleRequest($request);

        if ($form->isValid()) {

            $dm = $this->getDocumentManager();

            $dm->remove($page);
            $dm->flush();

            $this->get('braincrafted_bootstrap.flash')->success('Page deleted');

            return $this->redirect($this->generateUrl('integrated_website_page_index'));
        }

        return [
            'page' => $page,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Template
     */
    public function layoutAction(Page $page)
    {
        $pages = $this->getDocumentManager()->getRepository('IntegratedWebsiteBundle:Page\Page')->findAll();

//        $block = $this->getDocumentManager()->getRepository('IntegratedWebsiteBundle:Block\Block')->findOneBy(['shortName' => 'test']);
//
//        $item1 = new \Integrated\Bundle\WebsiteBundle\Document\Page\Grid\Item();
//        $item1->setBlock($block);
//
//        $column1 = new \Integrated\Bundle\WebsiteBundle\Document\Page\Grid\Column();
//        $column1->setSize(8);
//        $column1->setItems([$item1]);
//
//        $column2 = new \Integrated\Bundle\WebsiteBundle\Document\Page\Grid\Column();
//        $column2->setSize(4);
//
//        $row = new \Integrated\Bundle\WebsiteBundle\Document\Page\Grid\Row();
//        $row->setColumns([$column1, $column2]);
//
//        $item2 = new \Integrated\Bundle\WebsiteBundle\Document\Page\Grid\Item();
//        $item2->setRow($row);
//
//        $grid = new \Integrated\Bundle\WebsiteBundle\Document\Page\Grid\Grid('main');
//        $grid->setItems([$item1, $item2]);
//
//        $page->setGrids([$grid]);
//
//        $this->getDocumentManager()->flush();

        return [
            'page'  => $page,
            'pages' => $pages,
        ];
    }

    /**
     * @param Page $page
     * @return \Symfony\Component\Form\Form
     */
    protected function createCreateForm(Page $page)
    {
        $form = $this->createForm(
            'integrated_website_page',
            $page,
            [
                'action' => $this->generateUrl('integrated_website_page_new'),
                'method' => 'POST',
            ]
        );

        $form->add('submit', 'submit', ['label' => 'Save']);

        return $form;
    }

    /**
     * @param Page $page
     * @return \Symfony\Component\Form\Form
     */
    protected function createEditForm(Page $page)
    {
        $form = $this->createForm(
            'integrated_website_page',
            $page,
            [
                'action' => $this->generateUrl('integrated_website_page_edit', ['id' => $page->getId()]),
                'method' => 'PUT',
            ]
        );

        $form->add('submit', 'submit', ['label' => 'Save']);

        return $form;
    }

    /**
     * @param string $id
     * @return \Symfony\Component\Form\Form
     */
    protected function createDeleteForm($id)
    {
        $builder = $this->createFormBuilder();

        $builder->setAction($this->generateUrl('integrated_website_page_delete', ['id' => $id]));
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
}
