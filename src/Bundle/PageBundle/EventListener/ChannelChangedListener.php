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
use Integrated\Bundle\ContentBundle\Services\ContentTypeInformation;
use Integrated\Bundle\PageBundle\Document\Page\ContentTypePage;
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
     * @var ContentTypeInformation
     */
    private $contentTypeInformation;

    /**
     * @param DocumentManager        $dm
     * @param ContentTypePageService $contentTypePageService
     * @param RouteCache             $routeCache
     * @param ContentTypeInformation $contentTypeInformation
     */
    public function __construct(
        DocumentManager $dm,
        ContentTypePageService $contentTypePageService,
        RouteCache $routeCache,
        ContentTypeInformation $contentTypeInformation
    ) {
        $this->dm = $dm;
        $this->contentTypePageService = $contentTypePageService;
        $this->routeCache = $routeCache;
        $this->contentTypeInformation = $contentTypeInformation;
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

        $contentTypes = $this->getContentTypeRepository()->findAll();

        $routesChanged = false;

        /** @var ContentType $contentType */
        foreach ($contentTypes as $contentType) {
            if (!\in_array($contentType->getId(), $this->contentTypeInformation->getPublishingAllowedContentTypes($channel->getId()))) {
                $this->deletePagesByContentType($contentType, $channel->getId());
                continue;
            }

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
     * @param ContentType $contentType
     */
    protected function deletePagesByContentType(ContentType $contentType, $channelId)
    {
        $criteria = ['contentType.$id' => $contentType->getId()];
        $criteria['channel.$id'] = $channelId;

        $pages = $this->getPageRepository()->findBy($criteria);

        foreach ($pages as $page) {
            $this->dm->remove($page);
            $this->dm->flush($page);
        }
    }

    /**
     * @return \Doctrine\ODM\MongoDB\Repository\DocumentRepository
     */
    protected function getPageRepository()
    {
        return $this->dm->getRepository(ContentTypePage::class);
    }

    /**
     * @return \Doctrine\ODM\MongoDB\Repository\DocumentRepository
     */
    protected function getChannelRepository()
    {
        return $this->dm->getRepository(Channel::class);
    }

    /**
     * @return \Doctrine\ODM\MongoDB\Repository\DocumentRepository
     */
    protected function getContentTypeRepository()
    {
        return $this->dm->getRepository(ContentType::class);
    }
}
