<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Finder\Finder;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Integrated\Bundle\ContentBundle\Document\Channel\Channel;
use Integrated\Bundle\PageBundle\Document\Page\Page;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class PageController extends Controller
{
    /**
     * @Template
     *
     * @param Request $request
     *
     * @return array
     */
    public function indexAction(Request $request)
    {
        $channel = $this->getSelectedChannel();

        $builder = $this->getDocumentManager()->createQueryBuilder('IntegratedPageBundle:Page\Page');
        $builder->field('channel.$id')->equals($channel->getId());

        $pagination = $this->getPaginator()->paginate(
            $builder,
            $request->query->get('page', 1),
            20
        );

        return [
            'pages' => $pagination,
            'channels' => $this->getChannels(),
            'selectedChannel' => $channel,
        ];
    }

    /**
     * @Template
     *
     * @param Request $request
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function newAction(Request $request)
    {
        $page = new Page();

        $form = $this->createCreateForm($page);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $channel = $this->getSelectedChannel();

            $page->setChannel($channel);

            $dm = $this->getDocumentManager();

            $dm->persist($page);
            $dm->flush();

            $this->clearRoutingCache();

            $this->get('braincrafted_bootstrap.flash')->success('Page created');

            return $this->redirect($this->generateUrl('integrated_page_page_index', ['channel' => $channel->getId()]));
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @Template
     *
     * @param Request $request
     * @param Page $page
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function editAction(Request $request, Page $page)
    {
        $form = $this->createEditForm($page);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $channel = $this->getSelectedChannel();

            $this->getDocumentManager()->flush();

            $this->clearRoutingCache();

            $this->get('braincrafted_bootstrap.flash')->success('Page updated');

            return $this->redirect($this->generateUrl('integrated_page_page_index', ['channel' => $channel->getId()]));
        }

        return [
            'page' => $page,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Template
     *
     * @param Request $request
     * @param Page $page
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Page $page)
    {
        if ($page->isLocked()) {
            throw $this->createNotFoundException(sprintf('Page "%s" is locked.', $page->getId()));
        }

        $form = $this->createDeleteForm($page->getId());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $channel = $this->getSelectedChannel();

            $dm = $this->getDocumentManager();

            $dm->remove($page);
            $dm->flush();

            $this->clearRoutingCache();

            $this->get('braincrafted_bootstrap.flash')->success('Page deleted');

            return $this->redirect($this->generateUrl('integrated_page_page_index', ['channel' => $channel->getId()]));
        }

        return [
            'page' => $page,
            'form' => $form->createView(),
        ];
    }

    /**
     * @param Page $page
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function createCreateForm(Page $page)
    {
        $channel = $this->getSelectedChannel();

        $form = $this->createForm(
            'integrated_page_page',
            $page,
            [
                'action' => $this->generateUrl('integrated_page_page_new', ['channel' => $channel->getId()]),
                'method' => 'POST',
                'theme'  => $this->getTheme($channel),
            ]
        );

        $form->add('actions', 'integrated_save_cancel', [
            'cancel_route' => 'integrated_page_page_index',
            'cancel_route_parameters' => ['channel' => $this->getSelectedChannel()->getId()],
            'label' => 'Create',
            'button_class' => '',
        ]);

        return $form;
    }

    /**
     * @param Page $page
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function createEditForm(Page $page)
    {
        $channel = $page->getChannel();

        $form = $this->createForm(
            'integrated_page_page',
            $page,
            [
                'action' => $this->generateUrl(
                    'integrated_page_page_edit',
                    ['id' => $page->getId(), 'channel' => $channel->getId()]
                ),
                'method' => 'PUT',
                'theme'  => $this->getTheme($channel),
            ]
        );

        $form->add('actions', 'integrated_save_cancel', [
            'cancel_route' => 'integrated_page_page_index',
            'cancel_route_parameters' => ['channel' => $this->getSelectedChannel()->getId()],
        ]);

        return $form;
    }

    /**
     * @param string $id
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function createDeleteForm($id)
    {
        $builder = $this->createFormBuilder();

        $builder->setAction($this->generateUrl('integrated_page_page_delete', ['id' => $id]));
        $builder->setMethod('DELETE');
        $builder->add('submit', 'submit', ['label' => 'Delete', 'attr' => ['class' => 'btn-danger']]);

        return $builder->getForm();
    }

    /**
     * The routing cache needs to be cleared after a change.
     * This is faster then clearing the cache with the responsible command.
     */
    protected function clearRoutingCache()
    {
        $pattern = '/^app(.*)Url(Matcher|Generator).php/';

        $finder = new Finder();

        /** @var \Symfony\Component\Finder\SplFileInfo $file */
        foreach ($finder->files()->in($this->get('kernel')->getCacheDir())->depth(0)->name($pattern) as $file) {
            @unlink($file->getRealPath());
        }
    }

    /**
     * @return array
     */
    protected function getChannels()
    {
        $channels = [];

        foreach ($this->getChannelManager()->findAll() as $channel) {
            if ($configs = $this->getConfigResolver()->getConfigs($channel)) {
                foreach ($configs as $config) {
                    if ($config->getAdapter() === 'website') {
                        $channels[] = $channel;
                    }
                }
            }
        }

        return $channels;
    }

    /**
     * @return \Integrated\Common\Channel\ChannelInterface
     *
     * @throws \RuntimeException
     */
    protected function getSelectedChannel()
    {
        $request = $this->get('request_stack')->getCurrentRequest();

        if (!$request instanceof Request) {
            throw new \RuntimeException('Unable to get the request');
        }

        $channel = $this->getChannelManager()->find($request->query->get('channel'));

        if (!$channel instanceof Channel) {
            $channels = $this->getChannels();
            $channel  = reset($channels);
        }

        if (!$channel instanceof Channel) {
            throw new \RuntimeException('Please configure at least one channel');
        }

        return $channel;
    }

    /**
     * @param Channel $channel
     *
     * @return string
     */
    protected function getTheme(Channel $channel)
    {
        $theme = 'default';

        if ($configs = $this->getConfigResolver()->getConfigs($channel)) {
            foreach ($configs as $config) {
                if ($config->getAdapter() === 'website') {
                    $theme = $config->getOptions()->get('theme');
                    break;
                }
            }
        }

        return $theme;
    }

    /**
     * @return \Doctrine\ODM\MongoDB\DocumentManager
     */
    protected function getDocumentManager()
    {
        return $this->get('doctrine_mongodb')->getManager();
    }

    /**
     * @return \Knp\Component\Pager\Paginator
     */
    protected function getPaginator()
    {
        return $this->get('knp_paginator');
    }

    /**
     * @return \Integrated\Common\Channel\Connector\Config\ResolverInterface
     */
    protected function getConfigResolver()
    {
        return $this->get('integrated_channel.config.resolver');
    }

    /**
     * @return \Integrated\Common\Content\Channel\ChannelManagerInterface
     */
    protected function getChannelManager()
    {
        return $this->get('integrated_content.channel.manager');
    }
}
