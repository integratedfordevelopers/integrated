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

use Integrated\Bundle\UserBundle\Event\ConfigureRolesEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event subscriber for adding menu items to integrated_roles
 *
 * @author Vasil Pascal <developer.optimum@gmail.com>
 */
class ConfigureRolesSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            ConfigureRolesEvent::CONFIGURE => 'onRoleConfigure',
        );
    }

    /**
     * @param ConfigureRolesEvent $event
     */
    public function onRoleConfigure(ConfigureRolesEvent $event)
    {
        $event->addRoles([/*add you roles here array('ROLE_ADMIN', 'ROLE_USER')*/]);
    }
}
