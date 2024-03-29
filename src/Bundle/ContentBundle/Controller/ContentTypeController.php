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
use Integrated\Bundle\FormTypeBundle\Form\Type\FormActionsType;
use Integrated\Common\ContentType\ContentTypeInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Form;
use Integrated\Bundle\ContentBundle\Doctrine\ContentTypeManager;
use Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;
use Integrated\Bundle\ContentBundle\Form\Type\ContentTypeFormType;
use Integrated\Bundle\ContentBundle\Form\Type\DeleteFormType;
use Integrated\Common\ContentType\Event\ContentTypeEvent;
use Integrated\Common\ContentType\Events;
use Integrated\Common\Form\Mapping\MetadataFactory;
use Integrated\Common\Form\Mapping\MetadataFactoryInterface;
use Integrated\Common\Form\Mapping\MetadataInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ContentTypeController extends AbstractController
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
     * @var ContentTypeManager
     */
    private $contentTypeManager;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var DocumentManager
     */
    private $documentManager;

    /**
     * ContentTypeController constructor.
     *
     * @param ContentTypeManager       $contentTypeManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param MetadataFactory          $metadataFactory
     * @param DocumentManager          $documentManager
     */
    public function __construct(
        ContentTypeManager $contentTypeManager,
        EventDispatcherInterface $eventDispatcher,
        MetadataFactory $metadataFactory,
        DocumentManager $documentManager
    ) {
        $this->contentTypeManager = $contentTypeManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->metadata = $metadataFactory;
        $this->documentManager = $documentManager;
    }

    /**
     * Lists all the ContentType documents.
     *
     * @return Response
     */
    public function index()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $documents = $this->contentTypeManager->getAll();
        $documentTypes = $this->metadata->getAllMetadata();

        return $this->render('@IntegratedContent/content_type/index.html.twig', [
            'documents' => $documents,
            'documentTypes' => $documentTypes,
        ]);
    }

    /**
     * Display a list of Content documents.
     *
     * @return Response
     */
    public function select()
    {
        $documentTypes = $this->metadata->getAllMetadata();

        return $this->render('@IntegratedContent/content_type/select.html.twig', [
            'documentTypes' => $documentTypes,
        ]);
    }

    /**
     * Finds and displays a ContentType document.
     *
     * @param string $id
     *
     * @return Response
     */
    public function show($id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $contentType = $this->getContentType($id);
        $form = $this->createDeleteForm($contentType);

        return $this->render('@IntegratedContent/content_type/show.html.twig', [
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
    public function new(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $metadata = $this->metadata->getMetadata($request->get('class'));

        if (!$metadata) {
            return $this->redirectToRoute('integrated_content_content_type_select');
        }

        $contentType = new ContentType();
        $contentType->setClass($metadata->getClass());

        $form = $this->createNewForm($contentType, $metadata);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->documentManager->persist($contentType);
            $this->documentManager->flush();

            $this->addFlash('success', 'Item created');

            $this->eventDispatcher->dispatch(new ContentTypeEvent($contentType), Events::CONTENT_TYPE_CREATED);

            return $this->redirectToRoute('integrated_content_content_type_show', ['id' => $contentType->getId()]);
        }

        return $this->render('@IntegratedContent/content_type/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edits an existing ContentType document.
     *
     * @param Request $request
     * @param string  $id
     *
     * @return Response|RedirectResponse
     */
    public function edit(Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $contentType = $this->getContentType($id);
        $metadata = $this->metadata->getMetadata($contentType->getClass());

        $form = $this->createEditForm($contentType, $metadata);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->documentManager->contains($contentType)) {
                // Needed for content types from XML files
                $this->documentManager->persist($contentType);
            }

            $this->documentManager->flush();

            $this->addFlash('success', 'Item updated');

            $this->eventDispatcher->dispatch(new ContentTypeEvent($contentType), Events::CONTENT_TYPE_UPDATED);

            return $this->redirectToRoute('integrated_content_content_type_show', ['id' => $contentType->getId()]);
        }

        return $this->render('@IntegratedContent/content_type/edit.html.twig', [
            'form' => $form->createView(),
            'contentType' => $contentType,
        ]);
    }

    /**
     * Deletes a ContentType document.
     *
     * @param Request $request
     * @param string  $id
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $contentType = $this->getContentType($id);

        if ($contentType->isLocked()) {
            throw new AccessDeniedHttpException(sprintf('Content type with id "%s" is locked.', $id));
        }

        $form = $this->createDeleteForm($contentType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Only delete ContentType when there are no Content items
            $count = \count($this->documentManager->getRepository($contentType->getClass())->findBy(['contentType' => $contentType->getId()]));

            if ($count > 0) {
                // Set flash message and redirect to item page
                $this->addFlash('danger', 'Unable te delete, ContentType is not empty');

                return $this->redirectToRoute('integrated_content_content_type_show', ['id' => $contentType->getId()]);
            }

            $this->documentManager->remove($contentType);
            $this->documentManager->flush();

            $this->eventDispatcher->dispatch(new ContentTypeEvent($contentType), Events::CONTENT_TYPE_DELETED);

            // Set flash message
            $this->addFlash('success', 'Item deleted');

            return $this->redirectToRoute('integrated_content_content_type_index');
        }

        return $this->render('@IntegratedContent/content_type/delete.html.twig', [
            'contentType' => $contentType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param string $id
     *
     * @return ContentTypeInterface
     *
     * @throws NotFoundHttpException
     */
    private function getContentType($id)
    {
        try {
            return $this->contentTypeManager->getType($id);
        } catch (\InvalidArgumentException $e) {
            throw new NotFoundHttpException(sprintf('Content type with id "%s" not found.', $id));
        }
    }

    /**
     * Creates a form to create a ContentType document.
     *
     * @param ContentType       $type
     * @param MetadataInterface $metadata
     *
     * @return Form
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
     * @return Form
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
     * @return Form
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
