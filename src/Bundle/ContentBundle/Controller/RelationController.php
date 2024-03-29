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
use Integrated\Bundle\ContentBundle\Document\Relation\Relation;
use Integrated\Bundle\ContentBundle\Form\Type\RelationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class RelationController extends AbstractController
{
    /**
     * @var string
     */
    protected $relationClass = 'Integrated\\Bundle\\ContentBundle\\Document\\Relation\\Relation';

    private $documentManager;

    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    /**
     * Lists all the Relation documents.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $qb = $this->documentManager->createQueryBuilder($this->relationClass)
            ->sort('name');

        if ($contentType = $request->get('contentType')) {
            $qb->field('sources.$id')->in([(string) $contentType]);
        }

        $documents = $qb->getQuery()->execute();

        return $this->render(sprintf('@IntegratedContent/relation/index.%s.twig', $request->getRequestFormat()), ['documents' => $documents]);
    }

    /**
     * Finds and displays a Relation document.
     *
     * @param Relation $relation
     *
     * @return Response
     */
    public function show(Relation $relation)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createDeleteForm($relation);

        return $this->render('@IntegratedContent/relation/show.html.twig', [
            'form' => $form->createView(),
            'relation' => $relation,
        ]);
    }

    /**
     * Displays a form to create a new Relation document.
     *
     * @return Response
     */
    public function new()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createNewForm(new Relation());

        return $this->render('@IntegratedContent/relation/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Creates a new Relation document.
     *
     * @param Request $request
     *
     * @return Response|RedirectResponse
     */
    public function create(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $relation = new Relation();

        $form = $this->createNewForm($relation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->documentManager->persist($relation);
            $this->documentManager->flush();

            $this->addFlash('success', 'Item created');

            return $this->redirectToRoute('integrated_content_relation_index');
        }

        return $this->render('@IntegratedContent/relation/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Display a form to edit an existing Relation document.
     *
     * @param Relation $relation
     *
     * @return Response
     */
    public function edit(Relation $relation)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createEditForm($relation);

        return $this->render('@IntegratedContent/relation/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edits an existing Relation document.
     *
     * @param Request  $request
     * @param Relation $relation
     *
     * @return Response|RedirectResponse
     */
    public function update(Request $request, Relation $relation)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createEditForm($relation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->documentManager->flush();

            $this->addFlash('success', 'Item updated');

            return $this->redirectToRoute('integrated_content_relation_index');
        }

        return $this->render('@IntegratedContent/relation/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deletes a Relation document.
     *
     * @param Request  $request
     * @param Relation $relation
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, Relation $relation)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createDeleteForm($relation);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->documentManager->remove($relation);
            $this->documentManager->flush();

            $this->addFlash('success', 'Item deleted');
        }

        return $this->redirectToRoute('integrated_content_relation_index');
    }

    /**
     * Creates a form to create a Relation document.
     *
     * @param Relation $relation
     *
     * @return FormInterface
     */
    protected function createNewForm(Relation $relation)
    {
        $form = $this->createForm(
            RelationType::class,
            $relation,
            [
                'action' => $this->generateUrl('integrated_content_relation_create'),
                'method' => 'POST',
            ]
        );

        $form->add('submit', SubmitType::class, ['label' => 'Create']);

        return $form;
    }

    /**
     * Creates a form to edit a ContentType document.
     *
     * @param Relation $relation
     *
     * @return FormInterface
     */
    protected function createEditForm(Relation $relation)
    {
        $form = $this->createForm(
            RelationType::class,
            $relation,
            [
                'action' => $this->generateUrl('integrated_content_relation_update', ['id' => $relation->getId()]),
                'method' => 'PUT',
            ]
        );

        $form->add('submit', SubmitType::class, ['label' => 'Update']);

        return $form;
    }

    /**
     * Creates a form to delete a Relation document.
     *
     * @param Relation $relation
     *
     * @return FormInterface
     */
    protected function createDeleteForm(Relation $relation)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('integrated_content_relation_delete', ['id' => $relation->getId()]))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, [
                'label' => 'Delete',
                'attr' => [
                    'class' => 'btn-danger',
                    'onclick' => 'return confirm(\'Are you sure you want to delete this relation?\');',
                ],
            ])
            ->getForm()
        ;
    }
}
