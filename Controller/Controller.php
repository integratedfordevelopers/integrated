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

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Finder\Finder;

use Integrated\Bundle\ContentBundle\Document\Channel\Channel;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class Controller extends BaseController
{
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

        $channel = $this->getChannelManager()->find($request->attributes->get('_channel'));

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
