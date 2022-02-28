<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Queue;

use Countable;
use Serializable;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface QueueInterface extends Countable
{
    /**
     * Lowest priority in the queue.
     *
     * message marked with this priority will be handled last
     */
    public const PRIORITY_LOW = -10;

    /**
     * Medium-low priority in the queue.
     *
     * This is a priority in between low and medium.
     */
    public const PRIORITY_MEDIUM_LOW = -5;

    /**
     * Medium priority in the queue.
     *
     * Message marked with this priority will be handled after the high
     * priority messages are handled but before the low priority.
     *
     * This is the default priority if none is given.
     */
    public const PRIORITY_MEDIUM = 0;

    /**
     * Medium-high priority in the queue.
     *
     * This is a priority in between medium and high.
     */
    public const PRIORITY_MEDIUM_HIGH = 5;

    /**
     * Highest priority in the queue.
     *
     * Messages marked with this priority will be handled first.
     */
    public const PRIORITY_HIGH = 10;

    /**
     * Push the payload to the queue.
     *
     * @param string|Serializable $payload
     * @param int                 $delay
     * @param int                 $priority a priority number from -10 to and including 10
     *
     * @return mixed
     */
    public function push($payload, $delay = 0, $priority = self::PRIORITY_MEDIUM);

    /**
     * Pull one or more messages from the queue.
     *
     * @param int $limit
     *
     * @return QueueMessageInterface[]
     */
    public function pull($limit = 1);

    /**
     * @return int
     */
    public function count();

    /**
     * Clear all the message from the queue.
     */
    public function clear();
}
