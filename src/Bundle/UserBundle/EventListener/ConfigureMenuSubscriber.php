<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\EventListener;

use Integrated\Bundle\MenuBundle\Event\ConfigureMenuEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Event subscriber for adding menu items to integrated_menu.
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ConfigureMenuSubscriber implements EventSubscriberInterface
{
    const MENU = 'integrated_menu';
    const MENU_MANAGE = 'Manage';
    const ROLE_USER_MANAGER = 'ROLE_USER_MANAGER';
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
        return [
            ConfigureMenuEvent::CONFIGURE => 'onMenuConfigure',
        ];
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

        $admin = $this->authorizationChecker->isGranted(self::ROLE_ADMIN);

        if ($admin || $this->authorizationChecker->isGranted(self::ROLE_USER_MANAGER)) {
            if (!$menuManage = $menu->getChild(self::MENU_MANAGE)) {
                $menuManage = $menu->addChild(self::MENU_MANAGE);
            }

            $menuManage->addChild('Users', ['route' => 'integrated_user_user_index']);
            $menuManage->addChild('Groups', ['route' => 'integrated_user_group_index']);
            $menuManage->addChild('User scopes', ['route' => 'integrated_user_scope_index']);

            if ($admin) {
                $menuManage->addChild('IP List', ['route' => 'integrated_user_iplist_index']);
            }
        }
    }
}
