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
class QueueFactory implements QueueFactoryInterface
{
	private $provider;

	private $registry = array();

	public function __construct($provider)
	{
		$this->provider = $provider;
	}

	public function getQueue($channel)
	{
		if (!isset($this->registry[$channel])) {
			$this->registry[$channel] = new Queue($this->provider, $channel);
		}

		return $this->registry[$channel];
	}
}