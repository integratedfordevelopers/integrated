<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ChannelBundle\EventListener;

use Integrated\Bundle\MenuBundle\Event\ConfigureMenuEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Event subscriber for adding menu items to integrated_menu.
 *
 * @author Marijn Otte
 */
class ConfigureMenuSubscriber implements EventSubscriberInterface
{
    public const MENU = 'integrated_menu';
    public const MENU_ADMIN = 'Manage';
    public const ROLE_CHANNEL_MANAGER = 'ROLE_CHANNEL_MANAGER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [ConfigureMenuEvent::CONFIGURE => 'onMenuConfigure'];
    }

    /**
     * @param ConfigureMenuEvent $event
     */
    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu();
        if ($menu->getName() !== self::MENU) {
            return;
        }

        if ($this->authorizationChecker->isGranted(self::ROLE_CHANNEL_MANAGER) ||
            $this->authorizationChecker->isGranted(self::ROLE_ADMIN)) {
            if (!$menuAdmin = $menu->getChild(self::MENU_ADMIN)) {
                $menuAdmin = $menu->addChild(self::MENU_ADMIN);
            }

            $menuAdmin->addChild('Connectors', ['route' => 'integrated_channel_config_index']);
        }
    }
}
