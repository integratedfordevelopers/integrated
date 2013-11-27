<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr\Event;

use Integrated\Common\Solr\IndexerInterface;
use Integrated\Common\Solr\BatchOperationInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class BatchEvent extends IndexerEvent
{
	/**
	 * @var BatchOperationInterface
	 */
	protected $operation;

	/**
	 * Event constructor
	 *
	 * @param IndexerInterface $indexer
	 * @param BatchOperationInterface $operation
	 */
	public function __construct(IndexerInterface $indexer, BatchOperationInterface $operation)
	{
		parent::__construct($indexer);

		$this->operation = $operation;
	}

	/**
	 * Get the batch operation object for this event
	 *
	 * @return BatchOperationInterface
	 */
	public function getOperation()
	{
		return $this->operation;
	}
} 