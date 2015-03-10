<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Finder\Finder;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Integrated\Bundle\PageBundle\Document\Page\Page;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class PageController extends Controller
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
        $builder = $this->getDocumentManager()->createQueryBuilder('IntegratedPageBundle:Page\Page');

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
     *
     * @param Request $request
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
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

            $this->clearRoutingCache();

            $this->get('braincrafted_bootstrap.flash')->success('Page created');

            return $this->redirect($this->generateUrl('integrated_page_page_index'));
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @Template
     *
     * @param Request $request
     * @param Page $page
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function editAction(Request $request, Page $page)
    {
        $form = $this->createEditForm($page);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $this->getDocumentManager()->flush();

            $this->clearRoutingCache();

            $this->get('braincrafted_bootstrap.flash')->success('Page updated');

            return $this->redirect($this->generateUrl('integrated_page_page_index'));
        }

        return [
            'page' => $page,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Template
     *
     * @param Request $request
     * @param Page $page
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Page $page)
    {
        $form = $this->createDeleteForm($page->getId());
        $form->handleRequest($request);

        if ($form->isValid()) {

            $dm = $this->getDocumentManager();

            $dm->remove($page);
            $dm->flush();

            $this->clearRoutingCache();

            $this->get('braincrafted_bootstrap.flash')->success('Page deleted');

            return $this->redirect($this->generateUrl('integrated_page_page_index'));
        }

        return [
            'page' => $page,
            'form' => $form->createView(),
        ];
    }

    /**
     * @param Page $page
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function createCreateForm(Page $page)
    {
        $form = $this->createForm(
            'integrated_page_page',
            $page,
            [
                'action' => $this->generateUrl('integrated_page_page_new'),
                'method' => 'POST',
            ]
        );

        $form->add('submit', 'submit', ['label' => 'Save']);

        return $form;
    }

    /**
     * @param Page $page
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function createEditForm(Page $page)
    {
        $form = $this->createForm(
            'integrated_page_page',
            $page,
            [
                'action' => $this->generateUrl('integrated_page_page_edit', ['id' => $page->getId()]),
                'method' => 'PUT',
            ]
        );

        $form->add('submit', 'submit', ['label' => 'Save']);

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

        $builder->setAction($this->generateUrl('integrated_page_page_delete', ['id' => $id]));
        $builder->setMethod('DELETE');
        $builder->add('submit', 'submit', ['label' => 'Delete', 'attr' => ['class' => 'btn-danger']]);

        return $builder->getForm();
    }

    /**
     */
    protected function clearRoutingCache()
    {
        $pattern = '/^app(Dev|Prod)Url(Matcher|Generator).php/';

        $finder = new Finder();

        /** @var \Symfony\Component\Finder\SplFileInfo $file */
        foreach ($finder->files()->in($this->get('kernel')->getCacheDir())->depth(0)->name($pattern) as $file) {
            @unlink($file->getRealPath());
        }
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
