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

use Integrated\Bundle\ContentBundle\Document\Channel\Channel;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;
use Integrated\Bundle\PageBundle\Document\Page\ContentTypePage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Doctrine\ODM\MongoDB\DocumentManager;

use Integrated\Common\ContentType\Event\ContentTypeEvent;
use Integrated\Common\ContentType\Events;

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
     * @param DocumentManager $dm
     */
    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::CONTENT_TYPE_CREATED => 'contentTypeChanged',
            Events::CONTENT_TYPE_CHANGED => 'contentTypeChanged',
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
            $channels = $this->dm->getRepository('IntegratedContentBundle:Channel\Channel')->findAll();
            $pageRepo = $this->dm->getRepository('IntegratedPageBundle:Page\ContentTypePage');

            foreach ($channels as $channel) {
                if (!$pageRepo->findOneBy(['channel.$id' => $channel->getId(), 'contentType.$id' => $contentType->getId()])) {
                    $this->createNewPage($channel, $contentType);
                };
            }
        }
    }

    /**
     * @param Channel $channel
     * @param ContentType $contentType
     * @return ContentTypePage
     */
    protected function createNewPage(Channel $channel, ContentType $contentType)
    {
        $page = new ContentTypePage();
        $page->setContentType($contentType);
        $page->setChannel($channel);
        $page->setPath(sprintf('/content/%s/{slug}', $contentType->getId()));
        $page->setLayout('default.html.twig');

        $this->dm->persist($page);
        $this->dm->flush($page);

        return $page;
    }

}
