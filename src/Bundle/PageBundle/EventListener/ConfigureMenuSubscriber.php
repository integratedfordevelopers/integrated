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
    const MENU = 'integrated_menu';
    const MENU_WEBSITE = 'Website';
    const ROLE_WEBSITE_MANAGER = 'ROLE_WEBSITE_MANAGER';
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

        if ($this->authorizationChecker->isGranted(self::ROLE_WEBSITE_MANAGER) ||
            $this->authorizationChecker->isGranted(self::ROLE_ADMIN)) {
            if (!$menuWebsite = $menu->getChild(self::MENU_WEBSITE)) {
                $menuWebsite = $menu->addChild(self::MENU_WEBSITE);
            }

            $menuWebsite->addChild('Pages', ['route' => 'integrated_page_page_index']);
        }
    }
}
