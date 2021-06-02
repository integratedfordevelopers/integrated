<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\Security\Firewall;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

class IpListListener implements ListenerInterface
{
    public function handle(GetResponseEvent $event)
    {
        // All of this is just here to add a option to the firewall config.
    }
}
