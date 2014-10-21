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

use Serializable;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface QueueMessageInterface
{
	/**
	 * Delete the message from the queue.
	 */
	public function delete();

	/**
	 * Release the lock from the message so that an other queue worker can pick it up.
	 *
	 * @param int $delay
	 */
	public function release($delay = 0);

	/**
	 * The number of times this message has been picked up from the queue.
	 *
	 * @return int
	 */
	public function getAttempts();

	/**
	 * Get the message payload.
	 *
	 * @return mixed
	 */
	public function getPayload();

	/**
	 * Get the message priority
	 *
	 * @return int
	 */
	public function getPriority();
}
