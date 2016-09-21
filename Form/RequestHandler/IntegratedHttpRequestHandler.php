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

use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationRequestHandler;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Util\ServerParams;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class IntegratedHttpRequestHandler extends HttpFoundationRequestHandler
{
    /**
     * @var ContainerAwareEventDispatcher
     */
    private $dispatcher;

    /**
     * @param ServerParams $serverParams
     * @param ContainerAwareEventDispatcher $dispatcher
     */
    public function __construct(ServerParams $serverParams, ContainerAwareEventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;

        parent::__construct($serverParams);
    }

    /**
     * @param FormInterface $form
     * @param null $request
     */
    public function handleRequest(FormInterface $form, $request = null)
    {
        // Post an event
        $this->dispatcher->dispatch(
            IntegratedHttpRequestHandlerEvents::PRE_HANDLE,
            new HandleRequestEvent($form)
        );

        // Let the parent workout the details
        parent::handleRequest($form, $request);

        // This is the event where some hacking might occur
        $this->dispatcher->dispatch(
            IntegratedHttpRequestHandlerEvents::POST_HANDLE,
            new HandleRequestEvent($form)
        );
    }
}
