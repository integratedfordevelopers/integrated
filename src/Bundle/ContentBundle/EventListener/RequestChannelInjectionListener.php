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

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Integrated\Common\Content\Channel\ChannelContextInterface;
use Integrated\Common\Content\Channel\ChannelManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class RequestChannelInjectionListener implements EventSubscriberInterface
{
    /**
     * @var ChannelManagerInterface
     */
    private $manager;

    /**
     * @var ChannelContextInterface
     */
    private $context;

    /**
     * @param ChannelManagerInterface $manager
     * @param ChannelContextInterface $context
     */
    public function __construct(ChannelManagerInterface $manager, ChannelContextInterface $context)
    {
        $this->manager = $manager;
        $this->context = $context;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onRequest', 34],
        ];
    }

    /**
     * @param RequestEvent $event
     */
    public function onRequest(RequestEvent $event)
    {
        $channel = $this->getManager()->findByDomain($event->getRequest()->getHost());
        $this->getContext()->setChannel($channel);

        if ($channel
            && $channel->getPrimaryDomain()
            && strcasecmp($channel->getPrimaryDomain(), $event->getRequest()->getHost()) !== 0
            && $channel->getPrimaryDomainRedirect()
            && $event->getRequest()->getMethod() == 'GET'
        ) {
            $url = $event->getRequest()->getScheme().'://'.$channel->getPrimaryDomain().$event->getRequest()->getRequestUri();
            $event->setResponse(new RedirectResponse($url, 301));
        }
    }

    /**
     * @return ChannelManagerInterface
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @return ChannelContextInterface
     */
    public function getContext()
    {
        return $this->context;
    }
}
