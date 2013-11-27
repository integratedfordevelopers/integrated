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
use Solarium\QueryType\Update\Query\Query;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class SendEvent extends IndexerEvent
{
	/**
	 * @var Query
	 */
	protected $query;

	/**
	 * Event constructor
	 *
	 * @param IndexerInterface $indexer
	 * @param Query $batch
	 */
	public function __construct(IndexerInterface $indexer, Query $batch)
	{
		parent::__construct($indexer);

		$this->query = $batch;
	}

	/**
	 * Get the query object for this event
	 *
	 * @return Query
	 */
	public function getQuery()
	{
		return $this->query;
	}
} 