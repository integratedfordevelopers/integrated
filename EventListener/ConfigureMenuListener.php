<?php

namespace Integrated\Bundle\BlockBundle\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

use Integrated\Bundle\MenuBundle\Event\ConfigureMenuEvent;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class ConfigureMenuListener
 * @package Integrated\Bundle\BlockBundle\EventListener
 * @author Michael Jongman <michael@e-active.nl>
 */
class ConfigureMenuListener implements EventSubscriberInterface
{
    const ROLE_NEWSLETTER = 'ROLE_NEWSLETTER';

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
        if (!$this->authorizationChecker->isGranted(self::ROLE_NEWSLETTER)) {
            return;
        }

        $menu = $event->getMenu();
        if ($menu->getName() !== 'integrated_menu') {
            return;
        }

        $label = $menu->addChild('Manage');
        $label->addChild('Blocks', array('route' => 'integrated_block_block_index'));
    }
}
