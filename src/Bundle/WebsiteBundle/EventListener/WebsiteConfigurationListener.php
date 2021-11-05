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

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Integrated\Bundle\PageBundle\Resolver\ThemeResolver;
use Integrated\Bundle\ThemeBundle\Templating\ThemeManager;
use Integrated\Common\Content\Channel\ChannelContextInterface;
use Integrated\Common\Content\Channel\ChannelInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

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
     * @var ThemeResolver
     */
    private $resolver;

    /**
     * @param ChannelContextInterface $context
     * @param ThemeManager            $themeManager
     * @param ThemeResolver           $resolver
     */
    public function __construct(
        ChannelContextInterface $context,
        ThemeManager $themeManager,
        ThemeResolver $resolver
    ) {
        $this->context = $context;
        $this->themeManager = $themeManager;
        $this->resolver = $resolver;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 32],
        ];
    }

    /**
     * @param RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event)
    {
        $channel = $this->context->getChannel();

        if (!$channel instanceof ChannelInterface) {
            return;
        }

        $this->themeManager->setActiveTheme($this->resolver->getTheme($channel));
    }
}
