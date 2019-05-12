<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\PageBundle\EventListener;

use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\ContentBundle\Document\Channel\Channel;
use Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;
use Integrated\Bundle\PageBundle\Services\ContentTypePageService;
use Integrated\Bundle\PageBundle\Services\RouteCache;
use Integrated\Common\Channel\Event\ChannelEvent;
use Integrated\Common\Channel\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class ChannelChangedListener implements EventSubscriberInterface
{
    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @var ContentTypePageService
     */
    protected $contentTypePageService;

    /**
     * @var RouteCache
     */
    protected $routeCache;

    /**
     * @param DocumentManager $dm
     * @param ContentTypePageService $contentTypePageService
     * @param RouteCache $routeCache
     */
    public function __construct(DocumentManager $dm, ContentTypePageService $contentTypePageService, RouteCache $routeCache)
    {
        $this->dm = $dm;
        $this->contentTypePageService = $contentTypePageService;
        $this->routeCache = $routeCache;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::CHANNEL_CREATED => 'channelChanged',
            Events::CHANNEL_UPDATED => 'channelChanged',
            Events::CHANNEL_DELETED => 'channelDeleted',
        ];
    }

    /**
     * @param ChannelEvent $event
     */
    public function channelChanged(ChannelEvent $event)
    {
        $channel = $event->getChannel();

        $contentTypes = $this->getContentTypeRepository()->findBy(
            [
                'options.channels.disabled' => 0,
                'options.publication' => ['$ne' => 1],
                'options.channels.restricted' => ['$ne' => $channel->getId()]
            ]
        );

        $routesChanged = false;

        /** @var ContentType $contentType */
        foreach ($contentTypes as $contentType) {
            if (!$this->getPageRepository()->findOneBy([
                'channel.$id' => $channel->getId(),
                'contentType.$id' => $contentType->getId(),
            ])) {
                $this->contentTypePageService->addContentType($contentType, $channel);
                $routesChanged = true;
            }
        }

        if ($routesChanged) {
            $this->routeCache->clear();
        }
    }

    /**
     * @param ChannelEvent $event
     */
    public function channelDeleted(ChannelEvent $event)
    {
        $this->deletePagesByChannel($event->getChannel());
    }

    /**
     * @param Channel $channel
     */
    protected function deletePagesByChannel(Channel $channel)
    {
        $pages = $this->getPageRepository()->findBy(['channel.$id' => $channel->getId()]);

        foreach ($pages as $page) {
            $this->dm->remove($page);
            $this->dm->flush($page);
        }
    }

    /**
     * @return \Doctrine\ODM\MongoDB\DocumentRepository
     */
    protected function getPageRepository()
    {
        return $this->dm->getRepository('IntegratedPageBundle:Page\ContentTypePage');
    }

    /**
     * @return \Doctrine\ODM\MongoDB\DocumentRepository
     */
    protected function getChannelRepository()
    {
        return $this->dm->getRepository('IntegratedContentBundle:Channel\Channel');
    }

    /**
     * @return \Doctrine\ODM\MongoDB\DocumentRepository
     */
    protected function getContentTypeRepository()
    {
        return $this->dm->getRepository('IntegratedContentBundle:ContentType\ContentType');
    }
}
