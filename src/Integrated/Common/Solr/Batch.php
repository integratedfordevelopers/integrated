<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Batch implements BatchInterface
{
	private $batch = array();

	/**
	 * @inheritdoc
	 */
	public function add(BatchOperationInterface $operation)
	{
		$this->batch[] = $operation;
	}

	/**
	 * @inheritdoc
	 */
	public function remove(BatchOperationInterface $operation)
	{
		foreach ($this->batch as $key => $value) {
			if ($operation === $value) {
				unset($this->batch[$key]);
			}
		}
	}

	/**
	 * @inheritdoc
	 */
	public function clear()
	{
		$this->batch = array();
	}

	/**
	 * @inheritdoc
	 */
	public function count()
	{
		return count($this->batch);
	}

	/**
	 * @inheritdoc
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->batch);
	}
}