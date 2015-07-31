<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WebsiteBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

use Integrated\Common\Content\Channel\ChannelContextInterface;
use Integrated\Common\Content\Channel\ChannelInterface;
use Integrated\Bundle\ThemeBundle\Templating\ThemeManager;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class WebsiteConfigurationListener implements EventSubscriberInterface
{
    /**
     * @var ChannelContextInterface
     */
    private $context;

    /**
     * @var ThemeManager
     */
    private $themeManager;

    /**
     * @param ChannelContextInterface $context
     * @param ThemeManager $themeManager
     */
    public function __construct(ChannelContextInterface $context, ThemeManager $themeManager)
    {
        $this->context = $context;
        $this->themeManager = $themeManager;
    }

    /**s
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 32]
        ];
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $channel = $this->context->getChannel();

        if (!$channel instanceof ChannelInterface) {
            return;
        }

        $theme = 'default'; // @todo get theme from config

        $this->themeManager->setActiveTheme($theme);
    }
}
