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

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface QueueFactoryInterface
{
    /**
     * Get a queue instance for the given channel.
     *
     * @param string $channel
     *
     * @return QueueInterface
     */
    public function getQueue($channel);
}
