<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\EventListener;

use Integrated\Bundle\MenuBundle\Event\ConfigureMenuEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event subscriber for adding menu items to integrated_menu
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ConfigureMenuSubscriber implements EventSubscriberInterface
{
    const MENU_CONTENT = 'integrated_menu.content';
    const MENU_MANAGE = 'integrated_menu.manage';

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            ConfigureMenuEvent::CONFIGURE => 'onMenuConfigure'
        );
    }

    /**
     * @param ConfigureMenuEvent $event
     */
    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu();

        if (self::MENU_CONTENT === $menu->getName()) {
            $menu->addChild('Content navigator', array('route' => 'integrated_content_content_index'));
        }

        if (self::MENU_MANAGE === $menu->getName()) {
            $menu->addChild('Content types', array('route' => 'integrated_content_content_type_index'));
            $menu->addChild('Relations', array('route' => 'integrated_content_relation_index'));
        }
    }
}
