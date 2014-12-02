<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Queue\Provider;

use DateInterval;
use Integrated\Common\Queue\QueueMessageInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface QueueProviderInterface
{
	/**
	 * Push a message to the queue.
	 *
	 * The payload needs to be serializable or it could give problems
	 * with providers that need to store that payload as a string.
	 *
	 * @param string $channel  The channel to push the message to
	 * @param mixed  $payload  The payload
	 * @param int    $delay    The delay in seconds
	 * @param int    $priority The priority ranging from -10 to 10
	 */
	public function push($channel, $payload, $delay = 0, $priority = 0);

	/**
	 * Pull one or more messaged from the queue
	 *
	 * The message is not removed from the queue until the delete is called
	 * on the message.
	 *
	 * @param string $channel The channel to pull the message from
	 * @param int    $limit   Number of message to pull
	 *
	 * @return QueueMessageInterface[]
	 */
	public function pull($channel, $limit = 1);

	/**
	 * Clear a queue channel of al the messages
	 *
	 * @param string $channel The channel to clear
	 */
	public function clear($channel);

	/**
	 * Count the message in the queue.
	 *
	 * @param string $channel The channel to count the message in
	 * @return int
	 */
	public function count($channel);
}
