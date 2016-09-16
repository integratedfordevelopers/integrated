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

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Integrated\Bundle\ContentHistoryBundle\Event\ContentHistoryEvent;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class WorkflowSubscriber implements EventSubscriberInterface
{
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

    }
}
