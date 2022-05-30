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

use Integrated\Bundle\ContentBundle\Document\SearchSelection\SearchSelectionRepository;
use Doctrine\ODM\MongoDB\Query\Builder;
use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\ContentBundle\Document\SearchSelection\SearchSelection;
use Integrated\Bundle\ContentBundle\Form\Type\SearchSelectionType;
use Integrated\Bundle\ContentBundle\Services\SearchContentReferenced;
use Integrated\Bundle\IntegratedBundle\Controller\AbstractController;
use Integrated\Bundle\FormTypeBundle\Form\Type\SaveCancelType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class SearchSelectionController extends AbstractController
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var DocumentManager
     */
    private $documentManager;

    /**
     * @var SearchContentReferenced
     */
    private $searchContentReferenced;

    public function __construct(RequestStack $requestStack, DocumentManager $documentManager, SearchContentReferenced $searchContentReferenced)
    {
        $this->requestStack = $requestStack;
        $this->documentManager = $documentManager;
        $this->searchContentReferenced = $searchContentReferenced;
    }

    /**
     * Lists all the SearchSelection documents.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $paginator = $this->getPaginator()->paginate($this->getQueryBuilder(), $request->query->get('page', 1), 15);

        return $this->render('@IntegratedContent/search_selection/index.html.twig', [
            'searchSelections' => $paginator,
        ]);
    }

    /**
     * Creates a new SearchSelection document.
     *
     * @param Request $request
     *
     * @return Response|RedirectResponse
     */
    public function new(Request $request)
    {
        $searchSelection = new SearchSelection();

        $searchSelection->setFilters($request->query->all());
        $searchSelection->setUserId($this->getUser()->getId());

        $form = $this->createCreateForm($searchSelection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->documentManager->persist($searchSelection);
            $this->documentManager->flush();

            $this->addFlash('success', 'Item created');

            return $this->redirectToRoute('integrated_content_search_selection_index');
        }

        return $this->render('@IntegratedContent/search_selection/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edits an existing SearchSelection document.
     *
     * @param Request         $request
     * @param SearchSelection $searchSelection
     *
     * @return Response|RedirectResponse
     */
    public function edit(Request $request, SearchSelection $searchSelection)
    {
        // TODO: security check

        if ($searchSelection->isLocked()) {
            throw new AccessDeniedException();
        }

        $form = $this->createEditForm($searchSelection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->documentManager->flush();

            $this->addFlash('success', 'Item updated');

            return $this->redirectToRoute('integrated_content_search_selection_index');
        }

        return $this->render('@IntegratedContent/search_selection/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deletes a SearchSelection document.
     *
     * @param Request         $request
     * @param SearchSelection $searchSelection
     *
     * @return Response|RedirectResponse
     */
    public function delete(Request $request, SearchSelection $searchSelection)
    {
        // TODO: security check

        if ($searchSelection->isLocked()) {
            throw new AccessDeniedException();
        }

        $referenced = $this->searchContentReferenced->getReferenced($searchSelection);

        $form = $this->createDeleteForm($searchSelection->getId(), \count($referenced) > 0);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->documentManager->remove($searchSelection);
            $this->documentManager->flush();

            $this->addFlash('success', 'Item deleted');

            return $this->redirectToRoute('integrated_content_search_selection_index');
        }

        return $this->render('@IntegratedContent/search_selection/delete.html.twig', [
            'searchSelection' => $searchSelection,
            'form' => $form->createView(),
            'referenced' => $referenced,
        ]);
    }

    /**
     * Shows the menu.
     *
     * @return Response
     */
    public function menu()
    {
        /** @var Request $request */
        $request = $this->requestStack->getMainRequest();

        /** @var SearchSelectionRepository $repo */
        $repo = $this->documentManager->getRepository(SearchSelection::class);

        $user = $this->getUser();

        return $this->render('@IntegratedContent/search_selection/menu.html.twig', [
            'filters' => $request ? $request->query->all() : [],
            'searchSelections' => $user ? $repo->findPublicByUserId($user->getId()) : [],
        ]);
    }

    /**
     * Creates a form to create a SearchSelection document.
     *
     * @param SearchSelection $searchSelection
     *
     * @return FormInterface
     */
    protected function createCreateForm(SearchSelection $searchSelection)
    {
        /** @var Request $request */
        $request = $this->requestStack->getCurrentRequest();

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
     *
     * @return FormInterface
     */
    protected function createEditForm(SearchSelection $searchSelection)
    {
        $form = $this->createForm(
            SearchSelectionType::class,
            $searchSelection,
            [
                'action' => $this->generateUrl('integrated_content_search_selection_edit', ['id' => $searchSelection->getId()]),
                'method' => 'PUT',
            ]
        );

        $form->add('actions', SaveCancelType::class, ['cancel_route' => 'integrated_content_search_selection_index']);

        return $form;
    }

    /**
     * Creates a form to delete a SearchSelection document by id.
     *
     * @param $id
     * @param bool|false $notDelete
     *
     * @return FormInterface
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
     * @return Builder
     */
    protected function getQueryBuilder()
    {
        $builder = $this->documentManager->createQueryBuilder(SearchSelection::class);

        if (false === $this->isGranted('ROLE_ADMIN')) {
            $builder->field('userId')->equals($this->getUser()->getId());
        }

        return $builder;
    }
}
