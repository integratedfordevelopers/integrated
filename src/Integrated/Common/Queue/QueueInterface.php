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
	 * Push the payload to the queue
	 *
	 * @param string|Serializable $payload
	 * @param int $delay
	 * @return mixed
	 */
	public function push($payload, $delay = 0);

	/**
	 * Pull one or more messages from the queue
	 *
	 * @param int $limit
	 * @return QueueMessage
	 */
	public function pull($limit = 1);
}