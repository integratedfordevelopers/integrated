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
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Event subscriber for adding menu items to integrated_menu
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ConfigureMenuSubscriber implements EventSubscriberInterface
{
    const MENU = 'integrated_menu';
    const MENU_CONTENT = 'Content';
    const MENU_MANAGE = 'Manage';
    const ROLE_ADMIN = 'ROLE_ADMIN';

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

        if (!$menuContent = $menu->getChild(self::MENU_CONTENT)) {
            $menuContent = $menu->addChild(self::MENU_CONTENT);
        }

        $menuContent->addChild('Content navigator', array('route' => 'integrated_content_content_index'));
        $menuContent->addChild('Search selections', array('route' => 'integrated_content_search_selection_index'));

        if ($this->authorizationChecker->isGranted(self::ROLE_ADMIN)) {
            if (!$menuManage = $menu->getChild(self::MENU_MANAGE)) {
                $menuManage = $menu->addChild(self::MENU_MANAGE);
            }

            $menuManage->addChild('Content types', array('route' => 'integrated_content_content_type_index'));
            $menuManage->addChild('Channels', array('route' => 'integrated_content_channel_index'));
            $menuManage->addChild('Relations', array('route' => 'integrated_content_relation_index'));
        }
    }
}
