<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\BlockBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Doctrine\ODM\MongoDB\Query\Builder;

use Knp\Component\Pager\Event\ItemsEvent;

/**
 * Add all selected block ids to custom parameter
 *
 * @author Johan Liefers <johan@e-active.nl>
 */
class PaginateBlockOverviewSubscriber implements EventSubscriberInterface
{
    /**
     * @param ItemsEvent $event
     */
    public function items(ItemsEvent $event)
    {
        if (isset($event->options['query_type']) && 'block_overview' == $event->options['query_type']) {
            $builder = clone $event->target;

            if ($builder instanceof Builder) {
                $blocks = $builder->select('_id')
                    ->hydrate(false)
                    ->getQuery()
                    ->getIterator()
                    ->toArray();

                $event->setCustomPaginationParameter('blockIds', array_keys($blocks));
            }
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'knp_pager.items' => ['items', 20],
        ];
    }
}
