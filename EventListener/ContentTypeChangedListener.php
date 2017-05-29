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

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Doctrine\ODM\MongoDB\DocumentManager;

use Integrated\Common\ContentType\Event\ContentTypeEvent;
use Integrated\Common\ContentType\Events;
use Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;
use Integrated\Bundle\PageBundle\Services\ContentTypePageService;

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
     * @param DocumentManager $dm
     * @param ContentTypePageService $contentTypePageService
     */
    public function __construct(DocumentManager $dm, ContentTypePageService $contentTypePageService)
    {
        $this->dm = $dm;
        $this->contentTypePageService = $contentTypePageService;
    }

    /**
     * @inheritdoc
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
        $channelOption = $contentType->getOption('channels');

        if (isset($channelOption['disabled']) && $channelOption['disabled'] == 0) {
            $channels = $this->getChannelRepository()->findAll();

            foreach ($channels as $channel) {
                if (!$this->getPageRepository()->findOneBy([
                    'channel.$id' => $channel->getId(),
                    'contentType.$id' => $contentType->getId()
                ])) {
                    $this->contentTypePageService->addContentType($contentType, $channel);
                }
            }
        } else {
            $this->deletePagesByContentType($contentType);
        }
    }

    /**
     * @param ContentTypeEvent $event
     */
    protected function contentTypeDeleted(ContentTypeEvent $event)
    {
        $this->deletePagesByContentType($event->getContentType());
    }

    /**
     * @param ContentType $contentType
     */
    protected function deletePagesByContentType(ContentType $contentType)
    {
        $pages = $this->getPageRepository()->findBy(['contentType.$id' => $contentType->getId()]);

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
