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
use Integrated\Common\Solr\Indexer\QueueMessageInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class MessageEvent extends IndexerEvent
{
	/**
	 * @var QueueMessageInterface
	 */
	protected $message;

	/**
	 * Event constructor.
	 *
	 * @param IndexerInterface $indexer
	 * @param QueueMessageInterface $message
	 */
	public function __construct(IndexerInterface $indexer, QueueMessageInterface $message)
	{
		parent::__construct($indexer);

		$this->message = $message;
	}

	/**
	 * Get the queue message object for this event.
	 *
	 * @return QueueMessageInterface
	 */
	public function getMessage()
	{
		return $this->message;
	}
} 