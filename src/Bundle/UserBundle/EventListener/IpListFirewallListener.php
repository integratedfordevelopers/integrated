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

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Integrated\Common\Security\IpListMatcherInterface;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Http\FirewallMapInterface;

class IpListFirewallListener implements EventSubscriberInterface
{
    /**
     * @var FirewallMapInterface
     */
    private $map;

    /**
     * @var IpListMatcherInterface
     */
    private $matcher;

    public function __construct(FirewallMapInterface $map, IpListMatcherInterface $matcher)
    {
        $this->map = $map;
        $this->matcher = $matcher;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onRequest', 16],
        ];
    }

    public function onRequest(RequestEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        if ($this->map instanceof FirewallMap && $config = $this->map->getFirewallConfig($request)) {
            if (\in_array('ip_list', $config->getListeners(), true) && !$this->matcher->match($request)) {
                $response = new Response();
                $response->setStatusCode(403, 'IP address rejected');

                $event->setResponse($response);
            }
        }
    }
}
