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

use Integrated\Common\Solr\Exception\ExceptionInterface;
use Integrated\Common\Solr\IndexerInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ErrorEvent extends IndexerEvent
{
	/**
	 * @var ExceptionInterface
	 */
	private $exception;

	/**
	 * Event constructor
	 *
	 * @param IndexerInterface $indexer
	 * @param ExceptionInterface $exception
	 */
	public function __construct(IndexerInterface $indexer, ExceptionInterface $exception)
	{
		parent::__construct($indexer);

		$this->exception = $exception;
	}

	/**
	 * Get the exception object for this event
	 *
	 * @return ExceptionInterface
	 */
	public function getException()
	{
		return $this->exception;
	}
}