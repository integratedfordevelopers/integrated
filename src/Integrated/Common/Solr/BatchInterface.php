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
interface BatchInterface extends \Countable, \IteratorAggregate
{
	/**
	 * Add teh given batch operation to the batch
	 *
	 * @param BatchOperationInterface $operation
	 */
	public function add(BatchOperationInterface $operation);

	/**
	 * Remove the given batch operation from the batch
	 *
	 * @param BatchOperationInterface $operation
	 */
	public function remove(BatchOperationInterface $operation);

	/**
	 * Clear all the batch operation from the batch
	 */
	public function clear();

	/**
	 * Return the number of batch operations
	 *
	 * @return int
	 */
	public function count();

	/**
	 * Get a iterator to walk of the batch operations
	 *
	 * @return BatchOperationInterface[]
	 */
	public function getIterator();
}