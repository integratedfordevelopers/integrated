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
    const MENU = 'integrated_menu';
    const SUB_MENU = 'content';

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
        if ($menu->getName() !== self::MENU) {
            return;
        }

        if (!$subMenu = $menu->getChild(self::SUB_MENU)) {
            $subMenu = $menu->addChild(self::SUB_MENU);
        }

        $subMenu->addChild('Content navigator', array('route' => 'integrated_content_content_index'));
    }
}
