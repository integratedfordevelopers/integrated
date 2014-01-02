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
use Integrated\Bundle\ContentBundle\Form\Type as Form;
use Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;
use Integrated\Common\ContentType\Mapping\Metadata;

class ContentTypeController extends Controller
{
    /**
     * @var string
     */
    protected $contentTypeClass = 'Integrated\Bundle\ContentBundle\Document\ContentType\ContentType';

    /**
     * @var \Integrated\Common\Content\Reader\Document
     */
    protected $reader;

    /**
     * Lists all the ContentType documents
     *
     * @Template()
     * @return array
     */
    public function indexAction()
    {
        /* @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
        $dm = $this->get('doctrine_mongodb')->getManager();
        $documents = $dm->getRepository($this->contentTypeClass)->findAll();

        // Get al documentTypes
        $documentTypes = $this->getReader()->readAll();

        return array(
            'documents' => $documents,
            'documentTypes' => $documentTypes
        );
    }

    /**
     * Display a list of Content documents
     *
     * @Template()
     * @return array
     */
    public function selectAction()
    {
        // Get all the document types
        $documentTypes = $this->getReader()->readAll();

        return array(
            'documentTypes' => $documentTypes
        );
    }

    /**
     * Finds and displays a ContentType document
     *
     * @Template()
     * @param ContentType $contentType
     * @return array
     */
    public function showAction(ContentType $contentType)
    {
        // Create form
        $form = $this->createDeleteForm($contentType->getId());

        return array(
            'form' => $form->createView(),
            'contentType' => $contentType
        );
    }

    /**
     * Displays a form to create a new ContentType document
     *
     * @Template()
     * @param Request $request
     * @return array
     */
    public function newAction(Request $request)
    {
        // Validate request based on document param
        $documents = $this->getReader()->readAll();
        if (!isset($documents[$request->get('class')])) {
            return $this->redirect($this->generateUrl('integrated_content_contenttype_select'));
        }

        /* @var $metadata Metadata\ContentType */
        $metadata = $documents[$request->get('class')];

        // Create contentType
        $contentType = new ContentType();
        $contentType->setClass($metadata->getClass());

        // Create form
        $form = $this->createCreateForm($contentType, $metadata);

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * Creates a new ContentType document
     *
     * @Template("IntegratedContentBundle:ContentType:new.html.twig")
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function createAction(Request $request)
    {
        // Validate request based on document param
        $documents = $this->getReader()->readAll();
        $formData = $request->get('content_type');
        if (!isset($documents[$formData['class']])) {
            return $this->redirect($this->generateUrl('integrated_content_contenttype_select'));
        }

        /* @var $metadata Metadata\ContentType */
        $metadata = $documents[$formData['class']];

        // Create contentType
        $contentType = new ContentType();
        $contentType->setClass($metadata->getClass());

        // Create form
        $form = $this->createCreateForm($contentType, $metadata);

        // Validate request
        $form->handleRequest($request);
        if ($form->isValid()) {

            /* @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
            $dm = $this->get('doctrine_mongodb')->getManager();
            $dm->persist($contentType);
            $dm->flush();

            // Set flash message
            $this->get('braincrafted_bootstrap.flash')->success('Item created');

            return $this->redirect($this->generateUrl('integrated_content_contenttype_show', array('id' => $contentType->getId())));
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * Display a form to edit an existing ContentType document
     *
     * @Template
     * @param ContentType $contentType
     * @return array
     */
    public function editAction(ContentType $contentType)
    {
        // Get all the document types
        $documents = $this->getReader()->readAll();

        /* @var $metadata Metadata\ContentType */
        $metadata = $documents[$contentType->getClass()];

        // Create form
        $form = $this->createEditForm($contentType, $metadata);

        return array(
            'form' => $form->createView(),
            'contentType' => $contentType
        );
    }

    /**
     * Edits an existing ContentType document
     *
     * @Template("IntegratedContentBundle:ContentType:edit.html.twig")
     * @param Request $request
     * @param ContentType $contentType
     * @return array|\Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request, ContentType $contentType)
    {
        // Get all the document types
        $documents = $this->getReader()->readAll();

        /* @var $metadata Metadata\ContentType */
        $metadata = $documents[$contentType->getClass()];

        // Create form
        $form = $this->createEditForm($contentType, $metadata);

        // Validate request
        $form->handleRequest($request);
        if ($form->isValid()) {

            /* @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
            $dm = $this->get('doctrine_mongodb')->getManager();
            $dm->flush();

            // Set flash message
            $this->get('braincrafted_bootstrap.flash')->success('Item updated');

            return $this->redirect($this->generateUrl('integrated_content_contenttype_show', array('id' => $contentType->getId())));
        }

        return array(
            'form' => $form->createView(),
            'contentType' => $contentType
        );
    }

    /**
     * Deletes a ContentType document
     *
     * @param Request $request
     * @param ContentType $contentType
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, ContentType $contentType)
    {
        $form = $this->createDeleteForm($contentType->getId());
        $form->handleRequest($request);

        if ($form->isValid()) {

            /* @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
            $dm = $this->get('doctrine_mongodb')->getManager();

            // Only delete ContentType when there are no Content items
            $count = $dm->getRepository($contentType->getClass())->findBy(array('type' => $contentType->getType()))->count();
            if ($count > 0) {

                // Set flash message and redirect to item page
                $this->get('braincrafted_bootstrap.flash')->error('Unable te delete, ContentType is not empty');
                return $this->redirect($this->generateUrl('integrated_content_contenttype_show', array('id' => $contentType->getId())));

            } else {

                $dm->remove($contentType);
                $dm->flush();

                // Set flash message
                $this->get('braincrafted_bootstrap.flash')->success('Item deleted');
            }
        }

        return $this->redirect($this->generateUrl('integrated_content_contenttype_index'));
    }

    /**
     * Get reader document form service container
     *
     * @return \Integrated\Common\Content\Reader\Document
     */
    protected function getReader()
    {
        if (null === $this->reader) {
            $this->reader = $this->get('integrated_content.reader.document');
        }

        return $this->reader;
    }

    /**
     * Creates a form to create a ContentType document
     *
     * @param ContentType $contentType
     * @param Metadata\ContentType $metadata
     * @return \Symfony\Component\Form\Form
     */
    private function createCreateForm(ContentType $contentType, Metadata\ContentType $metadata)
    {
        $form = $this->createForm(
            new Form\ContentType($metadata, $this->get('doctrine_mongodb')->getManager()->getRepository($this->contentTypeClass)),
            $contentType,
            array(
                'action' => $this->generateUrl('integrated_content_contenttype_create'),
                'method' => 'POST',
            )
        );

        $form->add('submit', 'submit', array('label' => 'Save'));

        return $form;
    }

    /**
     * Creates a form to edit a ContentType document.
     *
     * @param ContentType $contentType
     * @param Metadata\ContentType $metadata
     * @return \Symfony\Component\Form\Form
     */
    private function createEditForm(ContentType $contentType, Metadata\ContentType $metadata)
    {
        $form = $this->createForm(
            new Form\ContentType($metadata, $this->get('doctrine_mongodb')->getManager()->getRepository($this->contentTypeClass)),
            $contentType,
            array(
                'action' => $this->generateUrl('integrated_content_contenttype_update', array('id' => $contentType->getId())),
                'method' => 'PUT',
            )
        );

        $form->add('submit', 'submit', array('label' => 'Save'));

        return $form;
    }

    /**
     * Creates a form to delete a Kitty entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('integrated_content_contenttype_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete', 'attr'=> array('class' => 'btn-danger')))
            ->getForm()
            ;
    }
}