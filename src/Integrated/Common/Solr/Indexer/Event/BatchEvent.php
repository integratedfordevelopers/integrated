<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr\Indexer\Event;

use Integrated\Common\Solr\Indexer\IndexerInterface;
use Integrated\Common\Solr\Indexer\BatchOperation;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class BatchEvent extends IndexerEvent
{
	/**
	 * @var BatchOperation
	 */
	protected $operation;

	/**
	 * Event constructor
	 *
	 * @param IndexerInterface $indexer
	 * @param BatchOperation $operation
	 */
	public function __construct(IndexerInterface $indexer, BatchOperation $operation)
	{
		parent::__construct($indexer);

		$this->operation = $operation;
	}

	/**
	 * Get the batch operation object for this event
	 *
	 * @return BatchOperation
	 */
	public function getOperation()
	{
		return $this->operation;
	}
} 