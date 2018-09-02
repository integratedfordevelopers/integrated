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

use Integrated\Common\Content\ExtensibleInterface;
use Integrated\Common\Content\Extension\Event\ContentEvent;
use Integrated\Common\Content\Extension\ExtensionInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ContentListener
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

    public function __invoke(ContentEvent $event, $eventName, EventDispatcherInterface $dispatcher)
    {
        $event = clone $event;
        $event->setData(null);

        $content = $event->getContent();

        if ($content instanceof ExtensibleInterface) {
            $event->setData($content->getExtensions()->get($this->extension->getName()));
        }

        \call_user_func($this->listener, $event, $eventName, $dispatcher);

        if ($content instanceof ExtensibleInterface) {
            $content->getExtensions()->set($this->extension->getName(), $event->getData());
        }
    }
}
