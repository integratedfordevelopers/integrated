<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content\Extension;

use Integrated\Common\Content\Extension\Event\Listener\CommonListener;
use Integrated\Common\Content\Extension\Event\Listener\ContentListener;
use Integrated\Common\Content\Extension\Event\Subscriber\ContentSubscriberInterface;
use Symfony\Component\EventDispatcher\EventDispatcher as BaseEventDispatcher;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class EventDispatcher extends BaseEventDispatcher
{
    public function addListener($eventName, $listener, $priority = 0)
    {
        if (\is_array($listener) && $listener[0] instanceof EventSubscriberInterface) {
            if ($listener[0] instanceof ContentSubscriberInterface) {
                $listener = new ContentListener($listener[0]->getExtension(), $listener);
            } else {
                $listener = new CommonListener($listener[0]->getExtension(), $listener);
            }
        }

        parent::addListener($eventName, $listener, $priority);
    }
}
