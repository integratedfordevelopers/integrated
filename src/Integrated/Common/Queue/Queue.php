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

use DateInterval;

use Integrated\Common\Queue\Provider\QueueProviderInterface;
use Integrated\Common\Queue\Exception\UnexpectedTypeException;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Queue implements QueueInterface
{
//	/**
//	 * Lowest priority in the queue.
//	 *
//	 * message marked with this priority will be handled last
//	 */
//	const PRIORITY_LOW         = -10;
//
//	/**
//	 * Medium-low priority in the queue.
//	 *
//	 * This is a priority in between low and medium.
//	 */
//	const PRIORITY_MEDIUM_LOW  = -5;
//
//	/**
//	 * Medium priority in the queue.
//	 *
//	 * Message marked with this priority will be handled after the high
//	 * priority messages are handled but before the low priority.
//	 *
//	 * This is the default priority if none is given.
//	 */
//	const PRIORITY_MEDIUM      = 0;
//
//	/**
//	 * Medium-high priority in the queue.
//	 *
//	 * This is a priority in between medium and high.
//	 */
//	const PRIORITY_MEDIUM_HIGH = 5;
//
//	/**
//	 * Highest priority in the queue.
//	 *
//	 * Messages marked with this priority will be handled first.
//	 */
//	const PRIORITY_HIGH        = 10;

	/**
	 * @var string
	 */
	protected $channel;

	/**
	 * @var QueueProviderInterface
	 */
	protected $provider;

	public function __construct(QueueProviderInterface $provider, $channel)
	{
		$this->provider = $provider;
		$this->channel  = $channel;
	}

	public function getChannel()
	{
		return $this->channel;
	}

	public function getProvider()
	{
		return $this->provider;
	}

	/**
	 * @inheritdoc
	 */
	public function push($payload, $delay = 0, $priority = self::PRIORITY_MEDIUM)
	{
		$this->provider->push($this->channel, $payload, $delay, $priority);
	}

	/**
	 * @inheritdoc
	 */
	public function pull($limit = 1)
	{
		return $this->provider->pull($this->channel, $limit);
	}

	/**
	 * @inheritdoc
	 */
	public function count()
	{
		return $this->provider->count($this->channel);
	}

	public function clear()
	{
		$this->provider->clear($this->channel);
	}
}