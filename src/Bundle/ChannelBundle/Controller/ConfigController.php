<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ChannelBundle\Controller;

use Exception;
use Integrated\Bundle\ChannelBundle\Event\FilterResponseConfigEvent;
use Integrated\Bundle\ChannelBundle\Event\FormConfigEvent;
use Integrated\Bundle\ChannelBundle\Event\GetResponseConfigEvent;
use Integrated\Bundle\ChannelBundle\Form\Type\ActionsType;
use Integrated\Bundle\ChannelBundle\Form\Type\ConfigFormType;
use Integrated\Bundle\ChannelBundle\Form\Type\DeleteFormType;
use Integrated\Bundle\ChannelBundle\IntegratedChannelEvents;
use Integrated\Bundle\ChannelBundle\Model\Config;
use Integrated\Common\Channel\Connector\Adapter\RegistryInterface;
use Integrated\Common\Channel\Connector\AdapterInterface;
use Integrated\Common\Channel\Connector\Config\ConfigManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ConfigController extends AbstractController
{
    /**
     * @var ConfigManagerInterface
     */
    protected $manager;

    /**
     * @var RegistryInterface
     */
    protected $registry;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * ConfigController constructor.
     *
     * @param ConfigManagerInterface   $manager
     * @param RegistryInterface        $registry
     * @param EventDispatcherInterface $dispatcher
     * @param ContainerInterface       $container
     */
    public function __construct(
        ConfigManagerInterface $manager,
        RegistryInterface $registry,
        EventDispatcherInterface $dispatcher,
        ContainerInterface $container
    ) {
        $this->manager = $manager;
        $this->registry = $registry;
        $this->dispatcher = $dispatcher;
        $this->container = $container;
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        if (!$this->isGranted('ROLE_CHANNEL_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        if ($pager = $this->getPaginator()) {
            return $this->render('IntegratedChannelBundle:config:index.html.twig', [
                'adapters' => $this->registry->getAdapters(),
                'pager' => $pager->paginate($this->manager->findAll(), $request->query->get('page', 1)),
            ]);
        }

        throw new HttpException(500, 'Paginator service not found');
    }

    /**
     * @param Request $request
     * @param string  $adapter
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request, $adapter)
    {
        if (!$this->isGranted('ROLE_CHANNEL_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        try {
            $adapter = $this->registry->getAdapter($adapter);
        } catch (Exception $e) {
            throw $this->createNotFoundException('Not Found', $e);
        }

        $data = new Config();
        $data->setAdapter($adapter->getManifest()->getName());

        $event = new GetResponseConfigEvent($data, $request);

        if ($this->dispatcher->dispatch($event, IntegratedChannelEvents::CONFIG_CREATE_REQUEST)->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->createNewForm($data, $adapter);
        $form->handleRequest($request);

        if ($form->get('actions')->getData() == 'cancel') {
            return $this->redirect($this->generateUrl('integrated_channel_config_index'));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            // first we need to persist, because we need an ID in the event
            // this is dispatched down below
            $this->manager->persist($data);

            $event = $this->dispatcher->dispatch(
                new FormConfigEvent($data, $request, $form),
                IntegratedChannelEvents::CONFIG_CREATE_SUBMITTED
            );

            if (!$response = $event->getResponse()) {
                $this->addFlash('success', sprintf('The config %s is saved', $data->getName()));

                $response = $this->redirect($this->generateUrl('integrated_channel_config_index'));
            }

            $this->dispatcher->dispatch(
                new FilterResponseConfigEvent($data, $request, $response),
                IntegratedChannelEvents::CONFIG_CREATE_RESPONSE
            );

            return $response;
        }

        return $this->render('IntegratedChannelBundle:config:new.html.twig', [
            'adapter' => $adapter,
            'data' => $data,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param string  $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id)
    {
        if (!$this->isGranted('ROLE_CHANNEL_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        /** @var Config $data */
        $data = $this->manager->find($id);

        if (!$data) {
            throw $this->createNotFoundException();
        }

        try {
            $adapter = $this->registry->getAdapter($data->getAdapter());
        } catch (Exception $e) {
            throw $this->createNotFoundException('Not Found', $e);
        }

        $event = new GetResponseConfigEvent($data, $request);

        if ($this->dispatcher->dispatch($event, IntegratedChannelEvents::CONFIG_EDIT_REQUEST)->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->createEditForm($data, $adapter);
        $form->handleRequest($request);

        if ($form->get('actions')->getData() == 'cancel') {
            return $this->redirect($this->generateUrl('integrated_channel_config_index'));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $event = $this->dispatcher->dispatch(
                new FormConfigEvent($data, $request, $form),
                IntegratedChannelEvents::CONFIG_EDIT_SUBMITTED
            );

            $this->manager->persist($data);

            if (!$response = $event->getResponse()) {
                $this->addFlash('success', sprintf('The changes to the config %s are saved', $data->getName()));

                $response = $this->redirect($this->generateUrl('integrated_channel_config_index'));
            }

            $this->dispatcher->dispatch(
                new FilterResponseConfigEvent($data, $request, $response),
                IntegratedChannelEvents::CONFIG_EDIT_RESPONSE
            );

            return $response;
        }

        return $this->render('IntegratedChannelBundle:config:edit.html.twig', [
            'adapter' => $adapter,
            'data' => $data,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function externalReturnAction(Request $request)
    {
        $session = new Session();

        if (!$id = $session->get('externalReturnId')) {
            $this->addFlash('danger', 'Config not found in session');

            return $this->indexAction($request);
        }

        return $this->editAction($request, $id);
    }

    /**
     * @param Request $request
     * @param string  $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request, $id)
    {
        if (!$this->isGranted('ROLE_CHANNEL_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        /** @var Config $data */
        $data = $this->manager->find($id);

        // It should always be possible to delete a connector even if the adaptor itself does
        // not exist anymore. So unlike the new and edit actions this action will not throw a
        // not found exception when the adaptor does not exist.

        if (!$data) {
            return $this->redirect($this->generateUrl('integrated_channel_config_index')); // data is already gone
        }

        $event = new GetResponseConfigEvent($data, $request);

        if ($this->dispatcher->dispatch($event, IntegratedChannelEvents::CONFIG_DELETE_REQUEST)->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->createDeleteForm($data);
        $form->handleRequest($request);

        if ($form->get('actions')->getData() == 'cancel') {
            return $this->redirect($this->generateUrl('integrated_channel_config_index'));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->remove($data);

            $this->addFlash('success', sprintf('The config %s is removed', $data->getName()));

            $response = $this->redirect($this->generateUrl('integrated_channel_config_index'));

            $this->dispatcher->dispatch(
                new FilterResponseConfigEvent($data, $request, $response),
                IntegratedChannelEvents::CONFIG_DELETE_RESPONSE
            );

            return $response;
        }

        return $this->render('IntegratedChannelBundle:config:delete.html.twig', [
            'adapter' => $this->registry->hasAdapter($data->getAdapter()) ? $this->registry->getAdapter($data->getAdapter()) : null,
            'data' => $data,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Config           $data
     * @param AdapterInterface $adapter
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function createNewForm(Config $data, AdapterInterface $adapter)
    {
        $form = $this->createForm(ConfigFormType::class, $data, [
            'adapter' => $adapter,
            'action' => $this->generateUrl(
                'integrated_channel_config_new',
                ['adapter' => $adapter->getManifest()->getName()]
            ),
            'method' => 'POST',
        ]);

        $form->add('actions', ActionsType::class, ['buttons' => ['create', 'cancel']]);

        return $form;
    }

    /**
     * @param Config           $data
     * @param AdapterInterface $adapter
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function createEditForm(Config $data, AdapterInterface $adapter)
    {
        $form = $this->createForm(ConfigFormType::class, $data, [
            'adapter' => $adapter,
            'action' => $this->generateUrl('integrated_channel_config_edit', ['id' => $data->getId()]),
            'method' => 'PUT',
        ]);

        $form->add('actions', ActionsType::class, ['buttons' => ['save', 'cancel']]);

        return $form;
    }

    /**
     * @param Config $data
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function createDeleteForm(Config $data)
    {
        $form = $this->createForm(DeleteFormType::class, $data, [
            'action' => $this->generateUrl('integrated_channel_config_delete', ['id' => $data->getId()]),
            'method' => 'DELETE',
        ]);

        $form->add('actions', ActionsType::class, ['buttons' => ['delete', 'cancel']]);

        return $form;
    }

    /**
     * @return \Knp\Component\Pager\Paginator
     */
    protected function getPaginator()
    {
        return $this->get('knp_paginator');
    }
}
