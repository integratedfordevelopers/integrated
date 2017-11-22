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

use Integrated\Bundle\ContentBundle\Document\SearchSelection\SearchSelection;
use Integrated\Bundle\ContentBundle\Form\Type\SearchSelectionType;
use Integrated\Bundle\FormTypeBundle\Form\Type\SaveCancelType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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

        if ($searchSelection->isLocked()) {
            throw new AccessDeniedException();
        }

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

        if ($searchSelection->isLocked()) {
            throw new AccessDeniedException();
        }

        $contentReferenced = $this->get('integrated_content.services.search.content.referenced');
        $referenced = $contentReferenced->getReferenced($searchSelection);

        $form = $this->createDeleteForm($searchSelection->getId(), count($referenced) > 0);
        $form->handleRequest($request);

        if ($form->has('submit') && $form->isValid()) {
            $this->getDocumentManager()->remove($searchSelection);
            $this->getDocumentManager()->flush();

            $this->get('braincrafted_bootstrap.flash')->success('Item deleted');

            return $this->redirect($this->generateUrl('integrated_content_search_selection_index'));
        }

        return [
            'searchSelection' => $searchSelection,
            'form' => $form->createView(),
            'referenced' => $referenced,
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
            SearchSelectionType::class,
            $searchSelection,
            [
                'action' => $this->generateUrl('integrated_content_search_selection_new', $request ? $request->query->all() : []),
                'method' => 'POST',
            ]
        );

        $form->add('actions', SaveCancelType::class, [
            'cancel_route' => 'integrated_content_search_selection_index',
            'label' => 'Create',
            'button_class' => '',
        ]);

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
            SearchSelectionType::class,
            $searchSelection,
            array(
                'action' => $this->generateUrl('integrated_content_search_selection_edit', ['id' => $searchSelection->getId()]),
                'method' => 'PUT',
            )
        );

        $form->add('actions', SaveCancelType::class, ['cancel_route' => 'integrated_content_search_selection_index']);

        return $form;
    }

    /**
     * Creates a form to delete a SearchSelection document by id.
     *
     * @param $id
     * @param bool|false $notDelete
     * @return \Symfony\Component\Form\Form
     */
    protected function createDeleteForm($id, $notDelete = false)
    {
        $form = $this
            ->createFormBuilder()
            ->setAction($this->generateUrl('integrated_content_search_selection_delete', ['id' => $id]))
            ->setMethod('DELETE');

        if ($notDelete) {
            $form->add('reload', SubmitType::class, ['label' => 'Reload', 'attr' => ['class' => 'btn-default']]);
        } else {
            $form->add('submit', SubmitType::class, ['label' => 'Delete', 'attr' => ['class' => 'btn-danger']]);
        }

        return $form->getForm();
    }

    /**
     * @return \Doctrine\ODM\MongoDB\Query\Builder
     */
    protected function getQueryBuilder()
    {
        $builder = $this->getDocumentManager()->createQueryBuilder('IntegratedContentBundle:SearchSelection\SearchSelection');

        if (false === $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
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
