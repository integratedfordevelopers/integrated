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

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Integrated\Common\ContentType\Mapping\Metadata;
use Integrated\Bundle\ContentBundle\Form\Type as Form;
use Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;
use Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\Relation;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ContentTypeRelationController extends Controller
{
    /**
     * Lists all the embedded Relations of a ContentType document
     *
     * @Template()
     * @param ContentType $contentType
     * @ParamConverter("contentType", class="Integrated\Bundle\ContentBundle\Document\ContentType\ContentType", options={"id" = "contentType"})
     * @return array
     */
    public function indexAction(ContentType $contentType)
    {
        return array(
            'contentType' => $contentType
        );
    }

    /**
     * Finds and displays a embedded Relation of a ContentType document.
     *
     * @Template()
     * @param ContentType $contentType
     * @param string $id The od of the Relation
     * @ParamConverter("contentType", class="Integrated\Bundle\ContentBundle\Document\ContentType\ContentType", options={"id" = "contentType"})
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return array
     */
    public function showAction(ContentType $contentType, $id)
    {
        /** @var $relation Relation */
        if (!$relation = $contentType->getRelation($id)) {
            throw $this->createNotFoundException('Relation not found');
        }

        // Create form
        $form = $this->createDeleteForm($contentType, $relation);

        return array(
            'form' => $form->createView(),
            'contentType' => $contentType,
            'relation' => $relation
        );
    }

    /**
     * Displays a form to create a new embedded Relation of a ContentType document.
     *
     * @Template()
     * @param ContentType $contentType
     * @ParamConverter("contentType", class="Integrated\Bundle\ContentBundle\Document\ContentType\ContentType", options={"id" = "contentType"})
     * @return array
     */
    public function newAction(ContentType $contentType)
    {
        // Create form
        $form = $this->createCreateForm($contentType);

        return array(
            'contentType' => $contentType,
            'form' => $form->createView()
        );
    }

    /**
     * Creates a new embedded Relation of a ContentType document.
     *
     * @Template("IntegratedContentBundle:ContentTypeRelation:new.html.twig")
     * @param Request $request
     * @param ContentType $contentType
     * @ParamConverter("contentType", class="Integrated\Bundle\ContentBundle\Document\ContentType\ContentType", options={"id" = "contentType"})
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function createAction(Request $request, ContentType $contentType)
    {
        // Create form
        $form = $this->createCreateForm($contentType);

        // Validate request
        $form->handleRequest($request);
        if ($form->isValid()) {

            /** @var Relation $relation */
            $relation = $form->getData();

            // Try to add Relation
            if (!$contentType->addRelation($relation)) {
                $form->get('name')->addError(new FormError('Relation with this name already exists'));
            } else {

                /** @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
                $dm = $this->get('doctrine_mongodb')->getManager();
                $dm->flush();

                // Set flash message
                $this->get('braincrafted_bootstrap.flash')->success('Relation added');

                return $this->redirect($this->generateUrl('integrated_content_content_type_show', array('id' => $contentType->getId())));
            }
        }

        return array(
            'contentType' => $contentType,
            'form' => $form->createView()
        );
    }

    /**
     * Deletes a embedded Relation of a ContentType document.
     *
     * @Template()
     * @param Request $request
     * @param ContentType $contentType
     * @param string $id The id of the Relation
     * @ParamConverter("contentType", class="Integrated\Bundle\ContentBundle\Document\ContentType\ContentType", options={"id" = "contentType"})
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, ContentType $contentType, $id)
    {
        /** @var $relation Relation */
        if (!$relation = $contentType->getRelation($id)) {
            throw $this->createNotFoundException('Relation not found');
        }
        $form = $this->createDeleteForm($contentType, $relation);
        $form->handleRequest($request);

        if ($form->isValid()) {

            if ($contentType->removeRelation($relation)) {

                /* @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
                $dm = $this->get('doctrine_mongodb')->getManager();
                $dm->flush();

                // Set flash message
                $this->get('braincrafted_bootstrap.flash')->success('Relation removed');

            } else {
                // Set flash message
                $this->get('braincrafted_bootstrap.flash')->error('Unable to remove relation');
            }
        }

        return $this->redirect($this->generateUrl('integrated_content_content_type_show', array('id' => $contentType->getId())));
    }

    /**
     * Display a form to edit an existing embedded Relation of a ContentType document
     *
     * @Template
     * @param ContentType $contentType
     * @param string $id The id of the Relation
     * @ParamConverter("contentType", class="Integrated\Bundle\ContentBundle\Document\ContentType\ContentType", options={"id" = "contentType"})
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return array
     */
    public function editAction(ContentType $contentType, $id)
    {
        /** @var $relation Relation */
        if (!$relation = $contentType->getRelation($id)) {
            throw $this->createNotFoundException('Relation not found');
        }

        // Create form
        $form = $this->createEditForm($contentType, $relation);

        return array(
            'form' => $form->createView(),
            'contentType' => $contentType
        );
    }

    /**
     * Edits an existing embedded Relation of a ContentType document
     *
     * @Template("IntegratedContentBundle:ContentTypeRelation:edit.html.twig")
     * @param Request $request
     * @param ContentType $contentType
     * @param string $id The id of the Relation
     * @ParamConverter("contentType", class="Integrated\Bundle\ContentBundle\Document\ContentType\ContentType", options={"id" = "contentType"})
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return array|\Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request, ContentType $contentType, $id)
    {
        /** @var $relation Relation */
        if (!$relation = $contentType->getRelation($id)) {
            throw $this->createNotFoundException('Relation not found');
        }

        // Create form
        $form = $this->createEditForm($contentType, $relation);

        // Validate request
        $form->handleRequest($request);
        if ($form->isValid()) {

            /* @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
            $dm = $this->get('doctrine_mongodb')->getManager();
            $dm->flush();

            // Set flash message
            $this->get('braincrafted_bootstrap.flash')->success('Relation updated');

            return $this->redirect($this->generateUrl('integrated_content_content_type_show', array('id' => $contentType->getId())));
        }

        return array(
            'form' => $form->createView(),
            'contentType' => $contentType
        );
    }

    /**
     * Creates a form to create a embedded Relation of a ContentType document
     *
     * @param ContentType $contentType
     * @return \Symfony\Component\Form\Form
     */
    private function createCreateForm(ContentType $contentType)
    {
        $form = $this->createForm(
            new Form\ContentTypeRelation(),
            null,
            array(
                'action' => $this->generateUrl('integrated_content_content_type_relation_create', array('contentType' => $contentType->getId())),
                'method' => 'POST',
            )
        );

        $form->add('submit', 'submit', array('label' => 'Save'));

        return $form;
    }

    /**
     * Creates a form to edit a embedded Relation of a ContentType document.
     *
     * @param ContentType $contentType
     * @param Relation $relation
     * @return \Symfony\Component\Form\Form
     */
    private function createEditForm(ContentType $contentType, Relation $relation)
    {
        $form = $this->createForm(
            new Form\ContentTypeRelation(),
            $relation,
            array(
                'action' => $this->generateUrl('integrated_content_content_type_relation_update', array('contentType' => $contentType->getId(), 'id' => $relation->getId())),
                'method' => 'PUT',
            )
        );

        $form->add('submit', 'submit', array('label' => 'Save'));

        return $form;
    }

    /**
     * Creates a form to delete a embedded Relation of a ContentType document
     *
     * @param ContentType $contentType
     * @param Relation $relation
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ContentType $contentType, Relation $relation)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('integrated_content_content_type_relation_delete', array('contentType' => $contentType->getId(), 'id' => $relation->getId())))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete', 'attr'=> array('class' => 'btn-danger')))
            ->getForm()
        ;
    }
}