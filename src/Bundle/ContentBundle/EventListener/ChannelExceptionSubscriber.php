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

use Integrated\Common\Content\Channel\ChannelContextInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Router;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class ChannelExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * @var Router
     */
    private $router;

    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @param Router                  $router
     * @param ChannelContextInterface $channelContext
     */
    public function __construct(Router $router, ChannelContextInterface $channelContext)
    {
        $this->router = $router;
        $this->channelContext = $channelContext;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [KernelEvents::EXCEPTION => 'onKernelException'];
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        if ($this->channelContext->getChannel()) {
            return;
        }

        $exception = $event->getException();

        if (!$exception instanceof NotFoundHttpException) {
            return;
        }

        if ($exception->getStatusCode() !== 404) {
            return;
        }

        $event->setResponse(new RedirectResponse($this->router->generate('integrated_content_content_index')));
    }
}
