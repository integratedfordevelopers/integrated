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
use Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;
use Integrated\Bundle\ContentBundle\Services\ContentTypeInformation;
use Integrated\Bundle\PageBundle\Services\ContentTypePageService;
use Integrated\Bundle\PageBundle\Services\RouteCache;
use Integrated\Common\ContentType\Event\ContentTypeEvent;
use Integrated\Common\ContentType\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class ContentTypeChangedListener implements EventSubscriberInterface
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
            Events::CONTENT_TYPE_UPDATED => 'contentTypeChanged',
            Events::CONTENT_TYPE_CREATED => 'contentTypeChanged',
            Events::CONTENT_TYPE_DELETED => 'contentTypeDeleted',
        ];
    }

    /**
     * @param ContentTypeEvent $event
     */
    public function contentTypeChanged(ContentTypeEvent $event)
    {
        $contentType = $event->getContentType();
        $newContentTypePage = false;

        $channels = $this->getChannelRepository()->findAll();

        foreach ($channels as $channel) {
            if (!\in_array($contentType->getId(), $this->contentTypeInformation->getPublishingAllowedContentTypes($channel->getId()))) {
                $this->deletePagesByContentType($contentType, $channel->getId());
                continue;
            }

            if (!$this->getPageRepository()->findOneBy([
                'channel.$id' => $channel->getId(),
                'contentType.$id' => $contentType->getId(),
            ])) {
                $newContentTypePage = true;
                $this->contentTypePageService->addContentType($contentType, $channel);
            }
        }
        if ($newContentTypePage) {
            $this->routeCache->clear();
        }
    }

    /**
     * @param ContentTypeEvent $event
     */
    public function contentTypeDeleted(ContentTypeEvent $event)
    {
        $this->deletePagesByContentType($event->getContentType());
    }

    /**
     * @param ContentType $contentType
     */
    protected function deletePagesByContentType(ContentType $contentType, $channelId = null)
    {
        $criteria = ['contentType.$id' => $contentType->getId()];
        if ($channelId !== null) {
            $criteria['channel.$id'] = $channelId;
        }

        $pages = $this->getPageRepository()->findBy($criteria);

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
}
