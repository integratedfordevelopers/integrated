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

use Integrated\Bundle\WebsiteBundle\Connector\WebsiteManifest;
use Integrated\Common\Channel\Connector\Config\ResolverInterface;
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
     * @var ResolverInterface
     */
    private $resolver;

    /**
     * @param ChannelContextInterface $context
     * @param ThemeManager $themeManager
     * @param ResolverInterface $resolver
     */
    public function __construct(
        ChannelContextInterface $context,
        ThemeManager $themeManager,
        ResolverInterface $resolver
    ) {
        $this->context = $context;
        $this->themeManager = $themeManager;
        $this->resolver = $resolver;
    }

    /**
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
        $channel = $this->context->getChannel();

        if (!$channel instanceof ChannelInterface) {
            return;
        }

        $theme = 'default';

        if ($configs = $this->resolver->getConfigs($channel)) {
            foreach ($configs as $config) {
                if ($config->getAdapter() === WebsiteManifest::NAME) {
                    $theme = $config->getOptions()->get('theme');
                    break;
                }
            }
        }

        $this->themeManager->setActiveTheme($theme);
    }
}
