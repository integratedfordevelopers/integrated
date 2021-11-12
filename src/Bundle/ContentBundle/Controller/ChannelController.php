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

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\ContentBundle\Document\Channel\Channel;
use Integrated\Bundle\ContentBundle\Form\Type as Form;
use Integrated\Bundle\ContentBundle\Services\SearchContentReferenced;
use Integrated\Common\Channel\Event\ChannelEvent;
use Integrated\Common\Channel\Events;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for CRUD actions Channel document.
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ChannelController extends AbstractController
{
    /**
     * @var DocumentManager
     */
    protected $documentManager;

    /**
     * @var SearchContentReferenced
     */
    protected $searchContentReferenced;

    /**
     * ChannelController constructor.
     *
     * @param DocumentManager         $documentManager
     * @param SearchContentReferenced $searchContentReferenced
     */
    public function __construct(DocumentManager $documentManager, SearchContentReferenced $searchContentReferenced)
    {
        $this->searchContentReferenced = $searchContentReferenced;
        $this->documentManager = $documentManager;
    }

    /**
     * Lists all the Channel documents.
     *
     * @return Response
     */
    public function indexAction()
    {
        if (!$this->isGranted('ROLE_CHANNEL_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $documents = $this->documentManager->getRepository(Channel::class)->findBy([], ['name' => 1]);

        return $this->render('IntegratedContentBundle:channel:index.html.twig', [
            'documents' => $documents,
        ]);
    }

    /**
     * Finds and displays a Channel document.
     *
     * @param Channel $channel
     *
     * @return Response
     */
    public function showAction(Channel $channel)
    {
        if (!$this->isGranted('ROLE_CHANNEL_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('IntegratedContentBundle:channel:show.html.twig', [
            'channel' => $channel,
        ]);
    }

    /**
     * Displays a form to create a new Channel document.
     *
     * @return Response
     */
    public function newAction()
    {
        if (!$this->isGranted('ROLE_CHANNEL_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $channel = new Channel();

        $form = $this->createCreateForm($channel);

        return $this->render('IntegratedContentBundle:channel:new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Creates a new Channel document.
     *
     * @param Request $request
     *
     * @return Response|RedirectResponse
     */
    public function createAction(Request $request)
    {
        if (!$this->isGranted('ROLE_CHANNEL_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $channel = new Channel();

        $form = $this->createCreateForm($channel);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->documentManager->persist($channel);
            $this->documentManager->flush();

            $this->get('braincrafted_bootstrap.flash')->success('Item created');

            $dispatcher = $this->get('integrated_content.event_dispatcher');
            $dispatcher->dispatch(new ChannelEvent($channel), Events::CHANNEL_CREATED);

            return $this->redirectToRoute('integrated_content_channel_show', ['id' => $channel->getId()]);
        }

        return $this->render('IntegratedContentBundle:channel:new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Display a form to edit an existing ContentType document.
     *
     * @param Channel $channel
     *
     * @return Response
     */
    public function editAction(Channel $channel)
    {
        if (!$this->isGranted('ROLE_CHANNEL_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createEditForm($channel);

        return $this->render('IntegratedContentBundle:channel:edit.html.twig', [
            'form' => $form->createView(),
            'channel' => $channel,
        ]);
    }

    /**
     * Edits an existing Channel document.
     *
     * @param Request $request
     * @param Channel $channel
     *
     * @return Response|RedirectResponse
     */
    public function updateAction(Request $request, Channel $channel)
    {
        if (!$this->isGranted('ROLE_CHANNEL_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createEditForm($channel);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->documentManager->flush();

            $this->get('braincrafted_bootstrap.flash')->success('Item updated');

            $dispatcher = $this->get('integrated_content.event_dispatcher');
            $dispatcher->dispatch(new ChannelEvent($channel), Events::CHANNEL_UPDATED);

            return $this->redirectToRoute('integrated_content_channel_show', ['id' => $channel->getId()]);
        }

        return $this->render('IntegratedContentBundle:channel:edit.html.twig', [
            'form' => $form->createView(),
            'channel' => $channel,
        ]);
    }

    /**
     * Deletes a Channel document.
     *
     * @param Request $request
     * @param Channel $channel
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, Channel $channel)
    {
        if (!$this->isGranted('ROLE_CHANNEL_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $referenced = $this->searchContentReferenced->getReferenced($channel);

        $form = $this->createDeleteForm($channel->getId(), \count($referenced) === 0);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->has('submit') && $form->get('submit')->isClicked()) {
            $this->documentManager->remove($channel);
            $this->documentManager->flush();

            $dispatcher = $this->get('integrated_content.event_dispatcher');
            $dispatcher->dispatch(new ChannelEvent($channel), Events::CHANNEL_DELETED);

            $this->get('braincrafted_bootstrap.flash')->success('Channel deleted');

            return $this->redirectToRoute('integrated_content_channel_index');
        }

        return $this->render('IntegratedContentBundle:channel:delete.html.twig', [
            'channel' => $channel,
            'form' => $form->createView(),
            'referenced' => $referenced,
        ]);
    }

    /**
     * Creates a form to create a ContentType document.
     *
     * @param Channel $channel
     *
     * @return FormInterface
     */
    protected function createCreateForm(Channel $channel)
    {
        $form = $this->createForm(
            Form\ChannelType::class,
            $channel,
            [
                'action' => $this->generateUrl('integrated_content_channel_create'),
                'method' => 'POST',
            ]
        );

        $form->add('submit', SubmitType::class, ['label' => 'Save']);

        return $form;
    }

    /**
     * Creates a form to edit a ContentType document.
     *
     * @param Channel $channel
     *
     * @return FormInterface
     */
    protected function createEditForm(Channel $channel)
    {
        $form = $this->createForm(Form\ChannelType::class, $channel, [
            'action' => $this->generateUrl('integrated_content_channel_update', ['id' => $channel->getId()]),
            'method' => 'PUT',
        ]);

        $form->add('submit', SubmitType::class, ['label' => 'Save']);

        return $form;
    }

    /**
     * Creates a form to delete a Channel document by id.
     *
     * @param mixed $id            The document id
     * @param bool  $deleteAllowed
     *
     * @return FormInterface
     */
    protected function createDeleteForm($id, bool $deleteAllowed)
    {
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('integrated_content_channel_delete', ['id' => $id]))
            ->setMethod('DELETE');

        if ($deleteAllowed) {
            $form->add('submit', SubmitType::class, ['label' => 'Delete', 'attr' => ['class' => 'btn-danger']]);
        } else {
            $form->add('reload', SubmitType::class, ['label' => 'Reload', 'attr' => ['class' => 'btn-default']]);
        }

        return $form->getForm();
    }
}
