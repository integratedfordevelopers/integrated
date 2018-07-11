<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Queue\Memory;

use Integrated\Common\Queue\QueueMessageInterface as BaseQueueMessageInterface;

interface QueueMessageInterface extends BaseQueueMessageInterface
{
    /**
     * The timestamp when this message is created.
     *
     * @return int
     */
    public function getCreatedAt(): int;

    /**
     * The timestamp when this message is updated.
     *
     * @return int
     */
    public function getUpdatedAt(): int;

    /**
     * The timestamp when this message should be picked up from the queue.
     *
     * @return int
     */
    public function getExecuteAt(): int;
}
