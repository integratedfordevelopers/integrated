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

use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Integrated\Common\Content\Channel\ChannelContextInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class ChannelExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    private $generator;

    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @param UrlGeneratorInterface   $generator
     * @param ChannelContextInterface $channelContext
     */
    public function __construct(UrlGeneratorInterface $generator, ChannelContextInterface $channelContext)
    {
        $this->generator = $generator;
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
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        if ($this->channelContext->getChannel()) {
            return;
        }

        $exception = $event->getThrowable();

        if (!$exception instanceof NotFoundHttpException) {
            return;
        }

        $event->setResponse(new RedirectResponse($this->generator->generate('integrated_content_content_index')));
    }
}
