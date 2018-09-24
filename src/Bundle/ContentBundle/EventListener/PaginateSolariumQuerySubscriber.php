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

use Knp\Component\Pager\Event\ItemsEvent;
use Solarium\Client;
use Solarium\QueryType\Select\Query\Query;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Solarium query pagination (to support max items).
 *
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class PaginateSolariumQuerySubscriber implements EventSubscriberInterface
{
    /**
     * @param ItemsEvent $event
     */
    public function items(ItemsEvent $event)
    {
        if (\is_array($event->target) && 2 == \count($event->target)) {
            list($client, $query) = array_values($event->target);

            if ($client instanceof Client && $query instanceof Query && isset($event->options['maxItems'])) {
                $maxItems = (int) $event->options['maxItems'];

                if ($maxItems > 0) {
                    $offset = $event->getOffset();
                    $limit = $event->getLimit();

                    $totalItems = $offset + $limit;

                    if (($totalItems - $maxItems) > $limit) {
                        $offset = floor($maxItems / $limit) * $limit;
                        $totalItems = $offset + $limit;
                    }

                    if ($totalItems > $maxItems) {
                        $limit = $limit - ($totalItems - $maxItems);
                    }

                    $query->setStart($offset)->setRows($limit);
                    $result = $client->select($query);

                    $event->items = $result->getIterator();
                    $event->count = $maxItems < $result->getNumFound() ? $maxItems : $result->getNumFound();

                    $event->setCustomPaginationParameter('result', $result);
                    $event->stopPropagation();
                }
            }
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'knp_pager.items' => ['items', 1],
        ];
    }
}
