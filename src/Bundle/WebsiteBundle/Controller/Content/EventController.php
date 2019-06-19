<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WebsiteBundle\Controller\Content;

use Integrated\Bundle\BlockBundle\Templating\BlockManager;
use Integrated\Bundle\ContentBundle\Document\Content\Event;
use Integrated\Bundle\PageBundle\Document\Page\ContentTypePage;
use Integrated\Bundle\ThemeBundle\Templating\ThemeManager;
use Integrated\Common\Content\Channel\ChannelContextInterface;
use Symfony\Bundle\TwigBundle\TwigEngine;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class EventController
{
    /**
     * @var ChannelContextInterface
     */
    protected $channelContext;

    /**
     * @var TwigEngine
     */
    protected $templating;

    /**
     * @var ThemeManager
     */
    protected $themeManager;

    /**
     * @var BlockManager
     */
    protected $blockManager;

    /**
     * @param TwigEngine   $templating
     * @param ThemeManager $themeManager
     * @param BlockManager $blockManager
     */
    public function __construct(ChannelContextInterface $channelContext, TwigEngine $templating, ThemeManager $themeManager, BlockManager $blockManager)
    {
        $this->channelContext = $channelContext;
        $this->templating = $templating;
        $this->themeManager = $themeManager;
        $this->blockManager = $blockManager;
    }

    /**
     * @param ContentTypePage $page
     * @param Event           $event
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(ContentTypePage $page, Event $event)
    {
        if (!$event->isPublished() || !$event->hasChannel($this->channelContext->getChannel())) {
            throw new NotFoundHttpException();
        }

        $this->blockManager->setDocument($event);

        return $this->templating->renderResponse(
            $this->themeManager->locateTemplate('content/event/show/'.$page->getLayout()),
            [
                'event' => $event,
                'page' => $page,
            ]
        );
    }
}
