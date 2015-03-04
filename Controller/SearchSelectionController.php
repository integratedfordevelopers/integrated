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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Integrated\Bundle\ContentBundle\Document\SearchSelection\SearchSelection;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class SearchSelectionController extends Controller
{
    /**
     * Lists all the SearchSelection documents.
     *
     * @Template
     *
     * @param Request $request
     *
     * @return array
     */
    public function indexAction(Request $request)
    {
        /** @var $paginator \Knp\Component\Pager\Paginator */
        $paginator = $this->get('knp_paginator');
        $paginator = $paginator->paginate($this->getQueryBuilder(), $request->query->get('page', 1), 15);

        return [
            'searchSelections' => $paginator,
        ];
    }

    /**
     * Creates a new SearchSelection document.
     *
     * @Template
     *
     * @param Request $request
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function newAction(Request $request)
    {
        $searchSelection = new SearchSelection();

        $searchSelection->setFilters($request->query->all());
        $searchSelection->setUserId($this->getUser()->getId());

        $form = $this->createCreateForm($searchSelection);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $this->getDocumentManager()->persist($searchSelection);
            $this->getDocumentManager()->flush();

            $this->get('braincrafted_bootstrap.flash')->success('Item created');

            return $this->redirect($this->generateUrl('integrated_content_search_selection_index'));
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * Edits an existing SearchSelection document.
     *
     * @Template
     *
     * @param Request $request
     * @param SearchSelection $searchSelection
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function editAction(Request $request, SearchSelection $searchSelection)
    {
        // TODO: security check

        $form = $this->createEditForm($searchSelection);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $this->getDocumentManager()->flush();

            $this->get('braincrafted_bootstrap.flash')->success('Item updated');

            return $this->redirect($this->generateUrl('integrated_content_search_selection_index'));
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * Deletes a SearchSelection document.
     *
     * @Template
     *
     * @param Request $request
     * @param SearchSelection $searchSelection
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, SearchSelection $searchSelection)
    {
        // TODO: security check

        $form = $this->createDeleteForm($searchSelection->getId());
        $form->handleRequest($request);

        if ($form->isValid()) {

            $this->getDocumentManager()->remove($searchSelection);
            $this->getDocumentManager()->flush();

            $this->get('braincrafted_bootstrap.flash')->success('Item deleted');

            return $this->redirect($this->generateUrl('integrated_content_search_selection_index'));
        }

        return [
            'searchSelection' => $searchSelection,
            'form' => $form->createView(),
        ];
    }

    /**
     * Shows the menu.
     *
     * @Template
     */
    public function menuAction()
    {
        /** @var Request $request */
        $request = $this->get('request_stack')->getMasterRequest();

        /** @var \Integrated\Bundle\ContentBundle\Document\SearchSelection\SearchSelectionRepository $repo */
        $repo = $this->getDocumentManager()->getRepository('IntegratedContentBundle:SearchSelection\SearchSelection');

        $user = $this->getUser();

        return [
            'filters' => $request ? $request->query->all() : [],
            'searchSelections' => $user ? $repo->findPublicByUserId($user->getId()) : [],
        ];
    }

    /**
     * Creates a form to create a SearchSelection document.
     *
     * @param SearchSelection $searchSelection
     * @return \Symfony\Component\Form\Form
     */
    protected function createCreateForm(SearchSelection $searchSelection)
    {
        /** @var Request $request */
        $request = $this->get('request_stack')->getCurrentRequest();

        $form = $this->createForm(
            $this->get('integrated_content.form.search_selection.type'),
            $searchSelection,
            array(
                'action' => $this->generateUrl('integrated_content_search_selection_new', $request ? $request->query->all() : []),
                'method' => 'POST',
            )
        );

        $form->add('submit', 'submit', ['label' => 'Save']);

        return $form;
    }

    /**
     * Creates a form to edit a SearchSelection document.
     *
     * @param SearchSelection $searchSelection
     * @return \Symfony\Component\Form\Form
     */
    protected function createEditForm(SearchSelection $searchSelection)
    {
        $form = $this->createForm(
            $this->get('integrated_content.form.search_selection.type'),
            $searchSelection,
            array(
                'action' => $this->generateUrl('integrated_content_search_selection_edit', ['id' => $searchSelection->getId()]),
                'method' => 'PUT',
            )
        );

        $form->add('submit', 'submit', ['label' => 'Save']);

        return $form;
    }

    /**
     * Creates a form to delete a SearchSelection document by id.
     *
     * @param mixed $id The document id
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createDeleteForm($id)
    {
        return $this
            ->createFormBuilder()
            ->setAction($this->generateUrl('integrated_content_search_selection_delete', ['id' => $id]))
            ->setMethod('DELETE')
            ->add('submit', 'submit', ['label' => 'Delete', 'attr' => ['class' => 'btn-danger']])
            ->getForm();
    }

    /**
     * @return \Doctrine\ODM\MongoDB\Query\Builder
     */
    protected function getQueryBuilder()
    {
        $builder = $this->getDocumentManager()->createQueryBuilder('IntegratedContentBundle:SearchSelection\SearchSelection');

        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $builder->field('userId')->equals($this->getUser()->getId());
        }

        return $builder;
    }

    /**
     * @return \Doctrine\ODM\MongoDB\DocumentManager
     */
    protected function getDocumentManager()
    {
        return $this->get('doctrine_mongodb')->getManager();
    }
}
