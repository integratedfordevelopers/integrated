<?php

namespace Integrated\Bundle\SolrBundle\EventListener;

use Knp\Component\Pager\Event\ItemsEvent;
use Knp\Component\Pager\Event\Subscriber\Paginate\SolariumQuerySubscriber as BaseSolariumQuerySubscriber;
use Traversable;

class SolariumQueryPageFixerSubscriber extends BaseSolariumQuerySubscriber
{
    public function items(ItemsEvent $event): void
    {
        parent::items($event);

        if ($event->isPropagationStopped() && $event->items instanceof Traversable) {
            $event->items = iterator_to_array($event->items);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'knp_pager.items' => ['items', 1], /* triggers before original */
        ];
    }
}
