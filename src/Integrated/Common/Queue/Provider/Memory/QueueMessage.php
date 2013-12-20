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
	private $payload;

	private $attempts;

	private $release = null;

	public function __construct($payload, $attempts, Closure $release)
	{
		$this->payload = $payload;
		$this->attempts = $attempts;

		$this->release = $release;
	}

	/**
	 * @inheritdoc
	 */
	public function delete()
	{
		// do nothing the message is already deleted.
	}

	/**
	 * @inheritdoc
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
	 * @inheritdoc
	 */
	public function getAttempts()
	{
		return $this->attempts;
	}

	/**
	 * @inheritdoc
	 */
	public function getPayload()
	{
		return $this->payload;
	}

}