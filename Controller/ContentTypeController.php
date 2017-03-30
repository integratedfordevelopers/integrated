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

use Braincrafted\Bundle\BootstrapBundle\Form\Type\FormActionsType;

use Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;
use Integrated\Bundle\ContentBundle\Form\Type\ContentTypeFormType;
use Integrated\Bundle\ContentBundle\Form\Type\DeleteFormType;
use Integrated\Common\Form\Mapping\MetadataFactoryInterface;
use Integrated\Common\Form\Mapping\MetadataInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ContentTypeController extends Controller
{
    /**
     * @var string
     */
    protected $contentTypeClass = 'Integrated\\Bundle\\ContentBundle\\Document\\ContentType\\ContentType';

    /**
     * @var MetadataFactoryInterface
     */
    protected $metadata;

    /**
     * Lists all the ContentType documents
     *
     * @Template
     *
     * @return array
     */
    public function indexAction()
    {
        /* @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
        $dm = $this->get('doctrine_mongodb')->getManager();
        $documents = $dm->getRepository($this->contentTypeClass)->findAll();

        $documentTypes = $this->getMetadata()->getAllMetadata();

        return [
            'documents' => $documents,
            'documentTypes' => $documentTypes
        ];
    }

    /**
     * Display a list of Content documents
     *
     * @Template
     *
     * @return array
     */
    public function selectAction()
    {
        $documentTypes = $this->getMetadata()->getAllMetadata();

        return [
            'documentTypes' => $documentTypes
        ];
    }

    /**
     * Finds and displays a ContentType document
     *
     * @Template
     *
     * @param ContentType $contentType
     * @return array
     */
    public function showAction(ContentType $contentType)
    {
        $form = $this->createDeleteForm($contentType);

        return [
            'form' => $form->createView(),
            'contentType' => $contentType
        ];
    }

    /**
     * Creates a new ContentType document
     *
     * @Template
     *
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function newAction(Request $request)
    {
        $metadata = $this->getMetadata()->getMetadata($request->get('class'));

        if (!$metadata) {
            return $this->redirect($this->generateUrl('integrated_content_content_type_select'));
        }

        $contentType = new ContentType();
        $contentType->setClass($metadata->getClass());

        $form = $this->createNewForm($contentType, $metadata);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /* @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
            $dm = $this->get('doctrine_mongodb')->getManager();
            $dm->persist($contentType);
            $dm->flush();

            $this->get('braincrafted_bootstrap.flash')->success('Item created');

            return $this->redirect($this->generateUrl('integrated_content_content_type_show', ['id' => $contentType->getId()]));
        }

        return [
            'form' => $form->createView()
        ];
    }

    /**
     * Edits an existing ContentType document
     *
     * @Template
     *
     * @param Request     $request
     * @param ContentType $contentType
     *
     * @return array|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, ContentType $contentType)
    {
        $metadata = $this->getMetadata()->getMetadata($contentType->getClass());

        $form = $this->createEditForm($contentType, $metadata);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /* @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
            $dm = $this->get('doctrine_mongodb')->getManager();
            $dm->flush();

            $this->get('braincrafted_bootstrap.flash')->success('Item updated');

            return $this->redirect($this->generateUrl('integrated_content_content_type_show', ['id' => $contentType->getId()]));
        }

        return [
            'form' => $form->createView(),
            'contentType' => $contentType
        ];
    }

    /**
     * Deletes a ContentType document
     *
     * @param Request     $request
     * @param ContentType $contentType
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, ContentType $contentType)
    {
        $form = $this->createDeleteForm($contentType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /* @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
            $dm = $this->get('doctrine_mongodb')->getManager();

            // Only delete ContentType when there are no Content items
            $count = count($dm->getRepository($contentType->getClass())->findBy(['contentType' => $contentType->getId()]));

            if ($count > 0) {
                // Set flash message and redirect to item page
                $this->get('braincrafted_bootstrap.flash')->error('Unable te delete, ContentType is not empty');

                return $this->redirect($this->generateUrl('integrated_content_content_type_show', ['id' => $contentType->getId()]));
            } else {
                $dm->remove($contentType);
                $dm->flush();

                // Set flash message
                $this->get('braincrafted_bootstrap.flash')->success('Item deleted');
            }
        }

        return $this->redirect($this->generateUrl('integrated_content_content_type_index'));
    }

    /**
     * Get the metadata factory form the service container
     *
     * @return MetadataFactoryInterface
     */
    protected function getMetadata()
    {
        if ($this->metadata === null) {
            $this->metadata = $this->get('integrated_content.metadata.factory');
        }

        return $this->metadata;
    }

    /**
     * Creates a form to create a ContentType document
     *
     * @param ContentType       $type
     * @param MetadataInterface $metadata
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function createNewForm(ContentType $type, MetadataInterface $metadata)
    {
        $form = $this->createForm(
            ContentTypeFormType::class,
            $type,
            [
                'action' => $this->generateUrl('integrated_content_content_type_new', ['class' => $type->getClass()]),
                'method' => 'POST',
                'metadata' => $metadata
            ],
            [
                'submit' => ['type' => SubmitType::class, 'options' => ['label' => 'Save']],
            ]
        );

        return $form;
    }

    /**
     * Creates a form to edit a ContentType document.
     *
     * @param ContentType       $type
     * @param MetadataInterface $metadata
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function createEditForm(ContentType $type, MetadataInterface $metadata)
    {
        $form = $this->createForm(
            ContentTypeFormType::class,
            $type,
            [
                'action' => $this->generateUrl('integrated_content_content_type_edit', ['id' => $type->getId()]),
                'method' => 'PUT',
                'metadata' => $metadata
            ],
            [
                'submit' => ['type' => SubmitType::class, 'options' => ['label' => 'Save']],
            ]
        );

        return $form;
    }

    /**
     * Creates a form to delete a ContentType document.
     *
     * @param ContentType $type
     * @return \Symfony\Component\Form\Form
     */
    protected function createDeleteForm(ContentType $type)
    {
        $form = $this->createForm(
            DeleteFormType::class,
            $type,
            [
                'action' => $this->generateUrl('integrated_content_content_type_delete', ['id' => $type->getId()]),
                'method' => 'DELETE',
            ],
            [
                'delete' => ['type' => SubmitType::class, 'options' => ['label' => 'Delete', 'attr' => ['class' => 'btn-danger']]],
            ]
        );

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function createForm($type, $data = null, array $options = [], array $buttons = [])
    {
        /** @var FormBuilder $form */
        $form = $this->container->get('form.factory')->createBuilder($type, $data, $options);

        if ($buttons) {
            $form->add('actions', FormActionsType::class, [
                'buttons' => $buttons
            ]);
        }

        return $form->getForm();
    }
}
