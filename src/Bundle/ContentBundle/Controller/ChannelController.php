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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Integrated\Bundle\ContentBundle\Document\Channel\Channel;
use Integrated\Bundle\ContentBundle\Form\Type as Form;
use Integrated\Common\Channel\Event\ChannelEvent;
use Integrated\Common\Channel\Events;

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
     * @Template()
     *
     * @return array
     */
    public function indexAction()
    {
        $documents = $this->getDocumentManager()->getRepository($this->channelClass)->findAll();

        return [
            'documents' => $documents,
        ];
    }

    /**
     * Finds and displays a Channel document.
     *
     * @Template()
     *
     * @param Channel $channel
     *
     * @return array
     */
    public function showAction(Channel $channel)
    {
        // Create form
        $form = $this->createDeleteForm($channel->getId());

        return [
            'form' => $form->createView(),
            'channel' => $channel,
        ];
    }

    /**
     * Displays a form to create a new Channel document.
     *
     * @Template()
     *
     * @return array
     */
    public function newAction()
    {
        // Create channel
        $channel = new Channel();

        // Create form
        $form = $this->createCreateForm($channel);

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * Creates a new Channel document.
     *
     * @Template("IntegratedContentBundle:Channel:new.html.twig")
     *
     * @param Request $request
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function createAction(Request $request)
    {
        // Create channel
        $channel = new Channel();

        // Create form
        $form = $this->createCreateForm($channel);

        // Validate request
        $form->handleRequest($request);
        if ($form->isValid()) {
            // Save channel
            $this->getDocumentManager()->persist($channel);
            $this->getDocumentManager()->flush();

            // Set flash message
            $this->get('braincrafted_bootstrap.flash')->success('Item created');

            $dispatcher = $this->get('integrated_content.event_dispatcher');
            $dispatcher->dispatch(Events::CHANNEL_CREATED, new ChannelEvent($channel));

            return $this->redirect($this->generateUrl('integrated_content_channel_show', ['id' => $channel->getId()]));
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * Display a form to edit an existing ContentType document.
     *
     * @Template
     *
     * @param Channel $channel
     *
     * @return array
     */
    public function editAction(Channel $channel)
    {
        // Create form
        $form = $this->createEditForm($channel);

        return [
            'form' => $form->createView(),
            'channel' => $channel,
        ];
    }

    /**
     * Edits an existing Channel document.
     *
     * @Template("IntegratedContentBundle:Channel:edit.html.twig")
     *
     * @param Request $request
     * @param Channel $channel
     *
     * @return array|\Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request, Channel $channel)
    {
        // Create form
        $form = $this->createEditForm($channel);

        // Validate request
        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->getDocumentManager()->flush();

            // Set flash message
            $this->get('braincrafted_bootstrap.flash')->success('Item updated');

            $dispatcher = $this->get('integrated_content.event_dispatcher');
            $dispatcher->dispatch(Events::CHANNEL_UPDATED, new ChannelEvent($channel));

            return $this->redirect($this->generateUrl('integrated_content_channel_show', ['id' => $channel->getId()]));
        }

        return [
            'form' => $form->createView(),
            'contentType' => $channel,
        ];
    }

    /**
     * Deletes a Channel document.
     *
     * @param Request $request
     * @param Channel $channel
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Channel $channel)
    {
        $form = $this->createDeleteForm($channel->getId());
        $form->handleRequest($request);

        if ($form->isValid()) {
            // Remove channel
            $this->getDocumentManager()->remove($channel);
            $this->getDocumentManager()->flush();

            $dispatcher = $this->get('integrated_content.event_dispatcher');
            $dispatcher->dispatch(Events::CHANNEL_DELETED, new ChannelEvent($channel));

            // Set flash message
            $this->get('braincrafted_bootstrap.flash')->success('Item deleted');
        }

        return $this->redirect($this->generateUrl('integrated_content_channel_index'));
    }

    /**
     * Creates a form to create a ContentType document.
     *
     * @param Channel $channel
     *
     * @return \Symfony\Component\Form\Form
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
     * @return \Symfony\Component\Form\Form
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
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('integrated_content_channel_delete', ['id' => $id]))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, ['label' => 'Delete', 'attr' => ['class' => 'btn-danger']])
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
