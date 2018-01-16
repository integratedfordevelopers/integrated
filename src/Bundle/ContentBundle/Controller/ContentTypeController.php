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
use Integrated\Common\ContentType\Event\ContentTypeEvent;
use Integrated\Common\ContentType\Events;
use Integrated\Common\Form\Mapping\MetadataFactoryInterface;
use Integrated\Common\Form\Mapping\MetadataInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * Lists all the ContentType documents.
     *
     * @return Response
     */
    public function indexAction()
    {
        /* @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
        $dm = $this->get('doctrine_mongodb')->getManager();
        $documents = $dm->getRepository($this->contentTypeClass)->findAll();

        $documentTypes = $this->getMetadata()->getAllMetadata();

        return $this->render('IntegratedContentBundle:content_type:index.html.twig', [
            'documents' => $documents,
            'documentTypes' => $documentTypes,
        ]);
    }

    /**
     * Display a list of Content documents.
     *
     * @return Response
     */
    public function selectAction()
    {
        $documentTypes = $this->getMetadata()->getAllMetadata();

        return $this->render('IntegratedContentBundle:content_type:select.html.twig', [
            'documentTypes' => $documentTypes,
        ]);
    }

    /**
     * Finds and displays a ContentType document.
     *
     * @param ContentType $contentType
     *
     * @return Response
     */
    public function showAction(ContentType $contentType)
    {
        $form = $this->createDeleteForm($contentType);

        return $this->render('IntegratedContentBundle:content_type:show.html.twig', [
            'form' => $form->createView(),
            'contentType' => $contentType,
        ]);
    }

    /**
     * Creates a new ContentType document.
     *
     * @param Request $request
     *
     * @return Response|RedirectResponse
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

            $dispatcher = $this->get('integrated_content.event_dispatcher');
            $dispatcher->dispatch(Events::CONTENT_TYPE_CREATED, new ContentTypeEvent($contentType));

            return $this->redirect($this->generateUrl('integrated_content_content_type_show', ['id' => $contentType->getId()]));
        }

        return $this->render('IntegratedContentBundle:content_type:new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edits an existing ContentType document.
     *
     * @param Request     $request
     * @param ContentType $contentType
     *
     * @return Response|RedirectResponse
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

            $dispatcher = $this->get('integrated_content.event_dispatcher');
            $dispatcher->dispatch(Events::CONTENT_TYPE_UPDATED, new ContentTypeEvent($contentType));

            return $this->redirect($this->generateUrl('integrated_content_content_type_show', ['id' => $contentType->getId()]));
        }

        return $this->render('IntegratedContentBundle:content_type:edit.html.twig', [
            'form' => $form->createView(),
            'contentType' => $contentType,
        ]);
    }

    /**
     * Deletes a ContentType document.
     *
     * @param Request     $request
     * @param ContentType $contentType
     *
     * @return RedirectResponse
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
            }
            $dm->remove($contentType);
            $dm->flush();

            $dispatcher = $this->get('integrated_content.event_dispatcher');
            $dispatcher->dispatch(Events::CONTENT_TYPE_DELETED, new ContentTypeEvent($contentType));

            // Set flash message
            $this->get('braincrafted_bootstrap.flash')->success('Item deleted');
        }

        return $this->redirect($this->generateUrl('integrated_content_content_type_index'));
    }

    /**
     * Get the metadata factory form the service container.
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
     * Creates a form to create a ContentType document.
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
                'metadata' => $metadata,
            ]
        );

        $form->add('actions', FormActionsType::class, [
            'buttons' => [
                'submit' => ['type' => SubmitType::class, 'options' => ['label' => 'Save']],
            ],
        ]);

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
                'metadata' => $metadata,
            ]
        );

        $form->add('actions', FormActionsType::class, [
            'buttons' => [
                'submit' => ['type' => SubmitType::class, 'options' => ['label' => 'Save']],
            ],
        ]);

        return $form;
    }

    /**
     * Creates a form to delete a ContentType document.
     *
     * @param ContentType $type
     *
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
            ]
        );

        $form->add('actions', FormActionsType::class, [
            'buttons' => [
                'delete' => ['type' => SubmitType::class, 'options' => ['label' => 'Delete', 'attr' => ['class' => 'btn-danger']]],
            ],
        ]);

        return $form;
    }
}
