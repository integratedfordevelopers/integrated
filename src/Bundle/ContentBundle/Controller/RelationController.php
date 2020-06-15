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

use Integrated\Bundle\ContentBundle\Document\Relation\Relation;
use Integrated\Bundle\ContentBundle\Form\Type\RelationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class RelationController extends Controller
{
    /**
     * @var string
     */
    protected $relationClass = 'Integrated\\Bundle\\ContentBundle\\Document\\Relation\\Relation';

    /**
     * Lists all the Relation documents.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $this->denyAccessUnlessGranted(['ROLE_ADMIN']);

        /* @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
        $dm = $this->get('doctrine_mongodb')->getManager();
        $qb = $dm->createQueryBuilder($this->relationClass)
            ->sort('name');

        if ($contentType = $request->get('contentType')) {
            $qb->field('sources.$id')->in([(string) $contentType]);
        }

        $documents = $qb->getQuery()->execute();

        return $this->render(sprintf('IntegratedContentBundle:relation:index.%s.twig', $request->getRequestFormat()), ['documents' => $documents]);
    }

    /**
     * Finds and displays a Relation document.
     *
     * @param Relation $relation
     *
     * @return Response
     */
    public function showAction(Relation $relation)
    {
        $this->denyAccessUnlessGranted(['ROLE_ADMIN']);

        $form = $this->createDeleteForm($relation);

        return $this->render('IntegratedContentBundle:relation:show.html.twig', [
            'form' => $form->createView(),
            'relation' => $relation,
        ]);
    }

    /**
     * Displays a form to create a new Relation document.
     *
     * @return Response
     */
    public function newAction()
    {
        $this->denyAccessUnlessGranted(['ROLE_ADMIN']);

        $form = $this->createNewForm(new Relation());

        return $this->render('IntegratedContentBundle:relation:new.html.twig', [
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
    public function createAction(Request $request)
    {
        $this->denyAccessUnlessGranted(['ROLE_ADMIN']);

        $relation = new Relation();

        $form = $this->createNewForm($relation);
        $form->handleRequest($request);

        if ($form->isValid()) {
            /* @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
            $dm = $this->get('doctrine_mongodb')->getManager();
            $dm->persist($relation);
            $dm->flush();

            $this->get('braincrafted_bootstrap.flash')->success('Item created');

            return $this->redirect($this->generateUrl('integrated_content_relation_index'));
        }

        return $this->render('IntegratedContentBundle:relation:new.html.twig', [
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
    public function editAction(Relation $relation)
    {
        $this->denyAccessUnlessGranted(['ROLE_ADMIN']);

        $form = $this->createEditForm($relation);

        return $this->render('IntegratedContentBundle:relation:edit.html.twig', [
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
    public function updateAction(Request $request, Relation $relation)
    {
        $this->denyAccessUnlessGranted(['ROLE_ADMIN']);

        $form = $this->createEditForm($relation);
        $form->handleRequest($request);

        if ($form->isValid()) {
            /* @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
            $dm = $this->get('doctrine_mongodb')->getManager();
            $dm->flush();

            $this->get('braincrafted_bootstrap.flash')->success('Item updated');

            return $this->redirect($this->generateUrl('integrated_content_relation_index'));
        }

        return $this->render('IntegratedContentBundle:relation:edit.html.twig', [
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
    public function deleteAction(Request $request, Relation $relation)
    {
        $this->denyAccessUnlessGranted(['ROLE_ADMIN']);

        $form = $this->createDeleteForm($relation);

        $form->handleRequest($request);
        if ($form->isValid()) {
            /* @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
            $dm = $this->get('doctrine_mongodb')->getManager();
            $dm->remove($relation);
            $dm->flush();

            $this->get('braincrafted_bootstrap.flash')->success('Item deleted');
        }

        return $this->redirect($this->generateUrl('integrated_content_relation_index'));
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
