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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
     * Lists all the Relation documents
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        /* @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
        $dm = $this->get('doctrine_mongodb')->getManager();
        $documents = $dm->getRepository($this->relationClass)->findAll();

        return $this->render('IntegratedContentBundle:Relation:index.html.twig', ['documents' => $documents]);
    }

    /**
     * Finds and displays a Relation document
     *
     * @param Relation $relation
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Relation $relation)
    {
        $form = $this->createDeleteForm($relation);

        return $this->render(
            'IntegratedContentBundle:Relation:show.html.twig',
            [
                'form' => $form->createView(),
                'relation' => $relation
            ]
        );
    }

    /**
     * Displays a form to create a new Relation document
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction()
    {
        $form = $this->createNewForm(new Relation());

        return $this->render(
            'IntegratedContentBundle:Relation:new.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Creates a new Relation document
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
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

        return $this->render(
            'IntegratedContentBundle:Relation:new.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Display a form to edit an existing Relation document
     *
     * @param Relation $relation
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Relation $relation)
    {
        $form = $this->createEditForm($relation);

        return $this->render(
            'IntegratedContentBundle:Relation:edit.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Edits an existing Relation document
     *
     * @param Request $request
     * @param Relation $relation
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request, Relation $relation)
    {
        $form = $this->createEditForm($relation);

        $form->handleRequest($request);
        if ($form->isValid()) {

            /* @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
            $dm = $this->get('doctrine_mongodb')->getManager();
            $dm->flush();

            $this->get('braincrafted_bootstrap.flash')->success('Item updated');

            return $this->redirect($this->generateUrl('integrated_content_relation_index'));
        }

        return $this->render(
            'IntegratedContentBundle:Relation:edit.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Deletes a Relation document
     *
     * @param Request $request
     * @param Relation $relation
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Relation $relation)
    {
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
     * Creates a form to create a Relation document
     *
     * @param Relation $relation
     * @return \Symfony\Component\Form\Form
     */
    protected function createNewForm(Relation $relation)
    {
        $form = $this->createForm(
            'integrated_relation',
            $relation,
            [
                'action'   => $this->generateUrl('integrated_content_relation_create'),
                'method'   => 'POST',
            ]
        );

        $form->add('submit', 'submit', ['label' => 'Create']);

        return $form;
    }

    /**
     * Creates a form to edit a ContentType document.
     *
     * @param Relation $relation
     * @return \Symfony\Component\Form\Form
     */
    protected function createEditForm(Relation $relation)
    {
        $form = $this->createForm(
            'integrated_relation',
            $relation,
            [
                'action'   => $this->generateUrl('integrated_content_relation_update', ['id' => $relation->getId()]),
                'method'   => 'PUT'
            ]
        );

        $form->add('submit', 'submit', ['label' => 'Update']);

        return $form;
    }

    /**
     * Creates a form to delete a Relation document.
     *
     * @param Relation $relation
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createDeleteForm(Relation $relation)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('integrated_content_relation_delete', ['id' => $relation->getId()]))
            ->setMethod('DELETE')
            ->add('submit', 'submit', ['label' => 'Delete', 'attr' => ['class' => 'btn-danger']])
            ->getForm()
        ;
    }
}
