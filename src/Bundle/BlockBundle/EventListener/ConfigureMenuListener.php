<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\BlockBundle\EventListener;

use Integrated\Bundle\MenuBundle\Event\ConfigureMenuEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @author Michael Jongman <michael@e-active.nl>
 */
class ConfigureMenuListener implements EventSubscriberInterface
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
        if (!$this->authorizationChecker->isGranted(self::ROLE_WEBSITE_MANAGER) &&
            !$this->authorizationChecker->isGranted(self::ROLE_ADMIN)) {
            return;
        }

        $menu = $event->getMenu();

        if ($menu->getName() !== self::MENU) {
            return;
        }

        if (!$label = $menu->getChild(self::MENU_WEBSITE)) {
            $label = $menu->addChild(self::MENU_WEBSITE);
        }

        $label->addChild('Blocks', ['route' => 'integrated_block_block_index']);
    }
}
