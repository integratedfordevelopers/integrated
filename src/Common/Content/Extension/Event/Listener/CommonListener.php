<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content\Extension\Event\Listener;

use Integrated\Common\Content\Extension\Event;
use Integrated\Common\Content\Extension\ExtensionInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class CommonListener
{
    /**
     * @var ExtensionInterface
     */
    protected $extension;

    /**
     * @var callable
     */
    protected $listener;

    public function __construct(ExtensionInterface $extension, callable $listener)
    {
        $this->extension = $extension;
        $this->listener = $listener;
    }

    public function __invoke(Event $event, $eventName, EventDispatcherInterface $dispatcher)
    {
        $event = clone $event;

        \call_user_func($this->listener, $event, $eventName, $dispatcher);
    }
}
