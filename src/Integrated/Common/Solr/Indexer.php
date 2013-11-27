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

use Integrated\Common\Solr\Event\BatchEvent;
use Integrated\Common\Solr\Event\ErrorEvent;
use Integrated\Common\Solr\Event\IndexerEvent;
use Integrated\Common\Solr\Event\MessageEvent;
use Integrated\Common\Solr\Event\ResultEvent;
use Integrated\Common\Solr\Event\SendEvent;
use Integrated\Common\Solr\Exception\ExceptionInterface;

use Solarium\Core\Client\Client;
use Solarium\QueryType\Update\Query\Command\Command;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Indexer implements IndexerInterface
{
	/**
	 * @var EventDispatcherInterface
	 */
	private $dispatcher;

	/**
	 * @var QueueInterface
	 */
	private $queue;

	/**
	 * @var BatchInterface
	 */
	private $batch = null;

	/**
	 * @var Client
	 */
	private $client = null;

	public function setEventDispatcher(EventDispatcherInterface $dispatcher)
	{
		$this->dispatcher = $dispatcher;
		return $this;
	}

	/**
	 * @return EventDispatcherInterface
	 */
	public function getEventDispatcher()
	{
		if ($this->dispatcher === null) {
			$this->dispatcher = new EventDispatcher();
		}

		return $this->dispatcher;
	}

	/**
	 * @inheritdoc
	 */
	public function setQueue(QueueInterface $queue)
	{
		$this->queue = $queue;
		return $this;
	}

	/**
	 * @return QueueInterface
	 */
	public function getQueue()
	{
		if ($this->queue === null) {
			$this->queue = null; // empty queue
		}

		return $this->queue;
	}

	/**
	 * @inheritdoc
	 */
	public function setSolr(Client $client)
	{
		$this->client = $client;
		return $this;
	}

	/**
	 * @return Client
	 */
	public function getSolr()
	{
		return $this->client;
	}

	/**
	 * @return BatchInterface
	 */
	protected function getBatch()
	{
		if ($this->batch === null) {
			$this->batch = new Batch();
		}

		return $this->batch;
	}

	/**
	 * @param QueueMessageInterface $data
	 * @return Command
	 */
	protected function getCommand(QueueMessageInterface $data)
	{
		switch ($data->getType()) {
			case 'ADD':

				break;

			case 'DELETE':

				break;

			case 'OPTIMIZE':

				break;

			case 'COMMIT':

				break;

			default:
//				throw new UnknownCommandException();
		}
	}

	/**
	 * @inheritdoc
	 */
	public function execute()
	{
		if ($this->getSolr() === null) {
//			throw new MissingSolrException();
		}

		try {
			$this->getEventDispatcher()->dispatch(Events::PRE_EXECUTE, new IndexerEvent($this));

			try {
				foreach ($this->getQueue()->get(1000) /* @TODO make $limit configurable */ as $data) {
					$this->batch($data);
				}

				$this->send(); // send the last batch if there is any
			} catch (ExceptionInterface $e) {
				$this->getEventDispatcher()->dispatch(Events::ERROR, new ErrorEvent($this, $e));
			}

			$this->clean();

			$this->getEventDispatcher()->dispatch(Events::POST_EXECUTE, new IndexerEvent($this));
		} catch (\Exception $e) {
			$this->clean(); // clean up before exiting

			throw $e;
		}
	}

	protected function batch(QueueMessageInterface $data)
	{
		$operation = new BatchOperation($data, $this->getCommand($data));

		// Send a event to allow the batch operation to be changed by
		// external code. After that check if the batch is cancels or
		// not. If canceled remove the message from the queue.

		$this->getEventDispatcher()->dispatch(Events::BATCHING, new BatchEvent($this, $operation));

		if ($operation->getCommand() === null) {
			$this->delete($data);
			return;
		}

		// make a clone so the batch operation it self can not be modified
		// by external code anymore.

		$batch = $this->getBatch();
		$batch->add(clone $operation);

		if ($batch->count() >= 10 /* @TODO make the count configurable */) {
			$this->send();
		}
	}

	protected function delete(QueueMessageInterface $data)
	{
		$this->getQueue()->delete($data);
		$this->getEventDispatcher()->dispatch(Events::PROCESSED, new MessageEvent($this, $data));
	}

	protected function send()
	{
		$batch = $this->getBatch();

		if ($batch->count() == 0) {
			return; // empty batch
		}

		// Build the query to send to the solr server

		$solr  = $this->getSolr();
		$query = $solr->createUpdate();

		foreach ($batch->getIterator() as $operation) {
			$query->add(null, $operation->getCommand());
		}

		$this->getEventDispatcher()->dispatch(Events::SENDING, new SendEvent($this, $query));

		try	{
			$result = $solr->execute($query);
		} catch (\Exception $e)	{
//			throw new InnerException($e);
		}

		$this->getEventDispatcher()->dispatch(Events::RESULTS, new ResultEvent($this, $result));

		// The query is executed so now the messages can be remove
		// from the queue.

		foreach ($batch->getIterator() as $operation) {
			$this->delete($operation->getMessage());
		}

		$batch->clear();
	}

	protected function clean()
	{
		if ($this->batch) {
			$this->batch->clear();
		}

		$this->batch = null;
	}
}