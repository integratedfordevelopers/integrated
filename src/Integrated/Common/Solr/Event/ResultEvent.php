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
use Solarium\Core\Query\Result\ResultInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ResultEvent extends IndexerEvent
{
	/**
	 * @var ResultInterface
	 */
	protected $result;

	/**
	 * Event constructor
	 *
	 * @param IndexerInterface $indexer
	 * @param ResultInterface $result
	 */
	public function __construct(IndexerInterface $indexer, ResultInterface $result)
	{
		parent::__construct($indexer);

		$this->result = $result;
	}

	/**
	 * Get the result object for this event
	 *
	 * @return ResultInterface
	 */
	public function getResult()
	{
		return $this->result;
	}
} 