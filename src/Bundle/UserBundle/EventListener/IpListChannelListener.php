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
use Integrated\Bundle\ContentBundle\Document\Channel\Channel;
use Integrated\Common\Content\Channel\ChannelContextInterface;
use Integrated\Common\Security\IpListMatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;

class IpListChannelListener implements EventSubscriberInterface
{
    /**
     * @var ChannelContextInterface
     */
    private $context;

    /**
     * @var IpListMatcherInterface
     */
    private $matcher;

    public function __construct(ChannelContextInterface $context, IpListMatcherInterface $matcher)
    {
        $this->context = $context;
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
        if (!$event->isMainRequest()) {
            return;
        }

        $channel = $this->context->getChannel();

        if ($channel instanceof Channel && $channel->isIpProtected() && !$this->matcher->match($event->getRequest())) {
            $response = new Response();
            $response->setStatusCode(403, 'IP address rejected');

            $event->setResponse($response);
        }
    }
}
