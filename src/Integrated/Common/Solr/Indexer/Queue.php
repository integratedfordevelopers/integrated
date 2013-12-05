<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr\Indexer;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Queue implements QueueInterface
{
	private $queue = array();

	/**
	 * @inheritdoc
	 */
	public function add(QueueMessageInterface $message)
	{
		$this->queue[spl_object_hash($message)] = $message;
		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function get($limit = 1)
	{
		return array_slice($this->queue, 0, $limit);
	}

	/**
	 * @inheritdoc
	 */
	public function delete(QueueMessageInterface $message)
	{
		$hash = spl_object_hash($message);

		if (isset($this->queue[$hash])) {
			unset($this->queue[$hash]);
		}

		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function count()
	{
		return count($this->queue);
	}
}