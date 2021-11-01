<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentHistoryBundle\EventListener;

use Integrated\Bundle\ContentHistoryBundle\Document\Embedded;
use Integrated\Bundle\ContentHistoryBundle\Event\ContentHistoryEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class RequestSubscriber implements EventSubscriberInterface
{
    /**
     * @var RequestStack|null
     */
    protected $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack = null)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            ContentHistoryEvent::INSERT => 'onChange',
            ContentHistoryEvent::UPDATE => 'onChange',
            ContentHistoryEvent::DELETE => 'onChange',
        ];
    }

    /**
     * @param ContentHistoryEvent $event
     */
    public function onChange(ContentHistoryEvent $event)
    {
        if ($this->requestStack instanceof RequestStack) {
            $masterRequest = $this->requestStack->getMasterRequest();

            if ($masterRequest instanceof Request) {
                $request = new Embedded\Request();

                $request->setIpAddress($masterRequest->getClientIp());
                $request->setEndpoint($masterRequest->getSchemeAndHttpHost().$masterRequest->getRequestUri());

                $event->getContentHistory()->setRequest($request);
            }
        }
    }
}
