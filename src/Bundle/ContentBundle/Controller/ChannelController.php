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

use Integrated\Bundle\ContentBundle\Document\Channel\Channel;
use Integrated\Bundle\ContentBundle\Form\Type as Form;
use Integrated\Common\Channel\Event\ChannelEvent;
use Integrated\Common\Channel\Events;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for CRUD actions Channel document.
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ChannelController extends Controller
{
    /**
     * @var string
     */
    protected $channelClass = 'Integrated\\Bundle\\ContentBundle\\Document\\Channel\\Channel';

    /**
     * @var \Doctrine\ODM\MongoDB\DocumentManager
     */
    protected $dm;

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

        $documents = $this->getDocumentManager()->getRepository($this->channelClass)->findAll();

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

        $form = $this->createDeleteForm($channel->getId());

        return $this->render('IntegratedContentBundle:channel:show.html.twig', [
            'form' => $form->createView(),
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
            $this->getDocumentManager()->persist($channel);
            $this->getDocumentManager()->flush();

            $this->get('braincrafted_bootstrap.flash')->success('Item created');

            $dispatcher = $this->get('integrated_content.event_dispatcher');
            $dispatcher->dispatch(Events::CHANNEL_CREATED, new ChannelEvent($channel));

            return $this->redirect($this->generateUrl('integrated_content_channel_show', ['id' => $channel->getId()]));
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
            $this->getDocumentManager()->flush();

            $this->get('braincrafted_bootstrap.flash')->success('Item updated');

            $dispatcher = $this->get('integrated_content.event_dispatcher');
            $dispatcher->dispatch(Events::CHANNEL_UPDATED, new ChannelEvent($channel));

            return $this->redirect($this->generateUrl('integrated_content_channel_show', ['id' => $channel->getId()]));
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

        $form = $this->createDeleteForm($channel->getId());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->getDocumentManager()->remove($channel);
            $this->getDocumentManager()->flush();

            $dispatcher = $this->get('integrated_content.event_dispatcher');
            $dispatcher->dispatch(Events::CHANNEL_DELETED, new ChannelEvent($channel));

            $this->get('braincrafted_bootstrap.flash')->success('Item deleted');
        }

        return $this->redirect($this->generateUrl('integrated_content_channel_index'));
    }

    /**
     * Creates a form to create a ContentType document.
     *
     * @param Channel $channel
     *
     * @return \Symfony\Component\Form\FormInterface
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
     * @return \Symfony\Component\Form\FormInterface
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
     * @param mixed $id The document id
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('integrated_content_channel_delete', ['id' => $id]))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, ['label' => 'Delete', 'attr' => ['onclick' => 'return confirm(\'Are you sure you want to delete this channel?\')', 'class' => 'btn-danger']])

            ->getForm();
    }

    /**
     * @return \Doctrine\ODM\MongoDB\DocumentManager
     */
    protected function getDocumentManager()
    {
        if (null === $this->dm) {
            $this->dm = $this->get('doctrine_mongodb')->getManager();
        }

        return $this->dm;
    }
}
