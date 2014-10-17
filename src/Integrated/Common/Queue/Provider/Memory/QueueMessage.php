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

use Closure;
use Integrated\Common\Queue\QueueMessageInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class QueueMessage implements QueueMessageInterface
{
	/**
	 * @var mixed
	 */
	private $payload;

	/**
	 * @var int
	 */
	private $attempts;

	/**
	 * @var int
	 */
	private $priority;

	/**
	 * @var Closure | null
	 */
	private $release = null;

	/**
	 * @param mixed $payload
	 * @param int $attempts
	 * @param int $priority
	 * @param callable $release
	 */
	public function __construct($payload, $attempts, $priority, Closure $release)
	{
		$this->payload = $payload;
		$this->attempts = $attempts;
		$this->priority = $priority;

		$this->release = $release;
	}

	/**
	 * {@inheritdoc}
	 */
	public function delete()
	{
		// release should be cleared as after a delete the message can not
		// be returned anymore;

		$this->release = null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function release($delay = 0)
	{
		if ($this->release !== null) {
			$release = $this->release;
			$release();
		}

		$this->release = null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAttempts()
	{
		return $this->attempts;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPayload()
	{
		return $this->payload;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPriority()
	{
		return $this->priority;
	}
}
