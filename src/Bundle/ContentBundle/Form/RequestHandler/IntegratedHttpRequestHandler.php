<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Form\RequestHandler;

use Integrated\Bundle\ContentBundle\Event\HandleRequestEvent;
use Integrated\Bundle\ContentBundle\Events\IntegratedHttpRequestHandlerEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationRequestHandler;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Util\ServerParams;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class IntegratedHttpRequestHandler extends HttpFoundationRequestHandler
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @param ServerParams             $serverParams
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(ServerParams $serverParams, EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;

        parent::__construct($serverParams);
    }

    /**
     * @param FormInterface $form
     * @param null          $request
     */
    public function handleRequest(FormInterface $form, $request = null)
    {
        // Post an event
        $this->dispatcher->dispatch(
            new HandleRequestEvent($form),
            IntegratedHttpRequestHandlerEvents::PRE_HANDLE
        );

        // Let the parent workout the details
        parent::handleRequest($form, $request);

        // This is the event where some hacking might occur
        $this->dispatcher->dispatch(
            new HandleRequestEvent($form),
            IntegratedHttpRequestHandlerEvents::POST_HANDLE
        );
    }
}
