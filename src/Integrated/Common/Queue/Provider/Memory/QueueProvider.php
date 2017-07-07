<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Queue\Provider\Memory;

use Integrated\Common\Queue\Provider\QueueProviderInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class QueueProvider implements QueueProviderInterface
{
    private $queue = array();

    /**
     * {@inheritdoc}
     */
    public function push($channel, $payload, $delay = 0, $priority = 0)
    {
        // this is a in memory queue so delay is ignored.
        // TODO: for now also ignore priority

        $channel = (string) $channel;

        if (!isset($this->queue[$channel])) {
            $this->queue[$channel] = [];
        }

        $this->queue[$channel][] = ['payload' => $payload, 'attempts' => 0, 'priority' => min(max((int) $priority, -10), 10)];
    }

    /**
     * {@inheritdoc}
     */
    public function pull($channel, $limit = 1)
    {
        $channel = (string) $channel;

        if (!isset($this->queue[$channel])) {
            return [];
        }

        $limit = (int) $limit;
        $limit = $limit > 1 ? $limit : 1;

        $results = [];

        foreach (array_splice($this->queue[$channel], 0, $limit) as $row) {
            $release = function () use ($channel, $row) {
                $row['attempts']++;
                array_unshift($this->queue[$channel], $row);
            };

            $results[] = new QueueMessage($row['payload'], $row['attempts'], $row['priority'], $release);
        }

        return $results;
    }

    /**
     * {@inheritdoc}
     */
    public function clear($channel)
    {
        $this->queue[$channel] = [];
    }

    /**
     * {@inheritdoc}
     */
    public function count($channel)
    {
        return isset($this->queue[$channel]) ? count($this->queue[$channel]) : 0;
    }
}
