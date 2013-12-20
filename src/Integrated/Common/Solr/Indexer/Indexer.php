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

use Exception;

use Integrated\Common\Queue\Queue;
use Integrated\Common\Queue\QueueInterface;
use Integrated\Common\Queue\QueueMessageInterface;
use Integrated\Common\Queue\Provider\Memory\QueueProvider;

use Integrated\Common\Solr\Indexer\Event\BatchEvent;
use Integrated\Common\Solr\Indexer\Event\ErrorEvent;
use Integrated\Common\Solr\Indexer\Event\IndexerEvent;
use Integrated\Common\Solr\Indexer\Event\MessageEvent;
use Integrated\Common\Solr\Indexer\Event\ResultEvent;
use Integrated\Common\Solr\Indexer\Event\SendEvent;

use Integrated\Common\Solr\Exception\ClientException;
use Integrated\Common\Solr\Exception\ExceptionInterface;
use Integrated\Common\Solr\Exception\InvalidArgumentException;
use Integrated\Common\Solr\Exception\OutOfBoundsException;
use Integrated\Common\Solr\Exception\SerializerException;

use Solarium\Core\Client\Client;

use Solarium\QueryType\Update\Query\Command\Add;
use Solarium\QueryType\Update\Query\Command\Command;
use Solarium\QueryType\Update\Query\Command\Commit;
use Solarium\QueryType\Update\Query\Command\Delete;
use Solarium\QueryType\Update\Query\Command\Optimize;
use Solarium\QueryType\Update\Query\Command\Rollback;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

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
	 * @var SerializerInterface
	 */
	private $serializer;

	/**
	 * @var Client
	 */
	private $client = null;

	/**
	 * @var Batch
	 */
	private $batch = null;

	/**
	 * Set the event dispatcher
	 *
	 * @param EventDispatcherInterface $dispatcher
	 * @return $this
	 */
	public function setEventDispatcher(EventDispatcherInterface $dispatcher)
	{
		$this->dispatcher = $dispatcher;
		return $this;
	}

	/**
	 * Get the event dispatcher.
	 *
	 * If no event dispatcher is set then a default EventDispatcher is created.
	 *
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
	 * Get the queue.
	 *
	 * If no queue is set then a empty Queue is created.
	 *
	 * @return QueueInterface
	 */
	public function getQueue()
	{
		if ($this->queue === null) {
			$this->queue = new Queue(new QueueProvider(), 'indexer');
		}

		return $this->queue;
	}

	/**
	 * @inheritdoc
	 */
	public function setSerializer(SerializerInterface $serializer)
	{
		$this->serializer = $serializer;
	}

	/**
	 * Get the serializer.
	 *
	 * If no queue is set then a default Serializer is created.
	 *
	 * @return SerializerInterface
	 */
	public function getSerializer()
	{
		if ($this->serializer === null) {
			$this->serializer = new Serializer();
		}

		return $this->serializer;
	}

	/**
	 * @inheritdoc
	 */
	public function setClient(Client $client)
	{
		$this->client = $client;
		return $this;
	}

	/**
	 * Get the solarium client
	 *
	 * @return Client|null
	 */
	public function getClient()
	{
		return $this->client;
	}

	/**
	 * Get the batch
	 *
	 * If no batch is set then one will be created.
	 *
	 * @return Batch
	 */
	protected function getBatch()
	{
		if ($this->batch === null) {
			$this->batch = new Batch();
		}

		return $this->batch;
	}

	/**
	 * @inheritdoc
	 */
	public function execute(Client $client = null)
	{
		if ($client !== null) {
			$this->setClient($client);
		}

		if ($this->getClient() === null) {
			throw new InvalidArgumentException(sprintf('No instance of a Solarium\Core\Client\Client has been inserted into the indexer.'));
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
		} catch (Exception $e) {
			$this->clean(); // clean up before exiting

			throw $e;
		}
	}

	/**
	 * A queue message it not send to the solr server but grouped in a
	 * batch to send more operations at ones.
	 *
	 * @param QueueMessageInterface $data
	 */
	protected function batch(QueueMessageInterface $data)
	{
		$operation = new BatchOperation($data, $this->convert($data));

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

	/**
	 * Convert a job into a solarium update command.
	 *
	 * @param JobInterface $job
	 *
	 * @return Command|null
	 *
	 * @throws OutOfBoundsException if the message type is empty or invalid
	 * @throws SerializerException if there was problem deserializing a document
	 */
	protected function convert(JobInterface $job)
	{
		if ($job->hasAction()) {
			throw new OutOfBoundsException(sprintf('The jobs action is empty, valid actions are "%s"', 'ADD, DELETE, OPTIMIZE, ROLLBACK or COMMIT'));
		}

		$command = null;

		// Action specifies which command class to use where the options got
		// optional argument. Except for the ADD and DELETE command. The ADD
		// command need to deserialize a document and for that need the options
		// "document.data", "document.class" and "document.format". And the
		// DELETE command needs a "id" or a "query" but both are also allowed.
		// if those requirements are not met then no command will be created
		// and the result will be null.
		//
		// Options that are not used are ignored and will not generated any
		// kind of error.

		switch (strtoupper($job->getAction())) {
			case 'ADD':

				if ($job->hasOption('document.data') && $job->hasOption('document.class') && $job->hasOption('document.format')) {
					try {
						$document = $this->getSerializer()->deserialize($job->hasOption('document.data'), $job->hasOption('document.class'), $job->hasOption('document.format'));
					} catch (Exception $e) {
						throw new SerializerException($e->getMessage(), $e->getCode(), $e);
					}

					$command = new Add();
					$command->addDocument($this->getConverter()->getDocment($document));

					if ($job->hasOption('overwrite')) {
						$command->setOverwrite((bool) $job->getOption('overwrite'));
					}

					if ($job->hasOption('commitwithin')) {
						$command->setCommitWithin((bool) $job->getOption('commitwithin'));
					}
				}

				break;

			case 'DELETE':

				if ($job->hasOption('id') || $job->hasOption('query')) {
					$command = new Delete();

					if ($job->hasOption('id')) {
						$command->addId($job->getOption('id'));
					}

					if ($job->hasOption('query')) {
						$command->addQuery($job->getOption('query'));
					}
				}

				break;

			case 'OPTIMIZE':
				$command = new Optimize();

				if ($job->hasOption('maxsegments')) {
					$command->setMaxSegments((bool) $job->getOption('maxsegments'));
				}

				if ($job->hasOption('waitsearcher')) {
					$command->setWaitSearcher((bool) $job->getOption('waitsearcher'));
				}

				if ($job->hasOption('softcommit')) {
					$command->setSoftCommit((bool) $job->getOption('softcommit'));
				}

				break;

			case 'COMMIT':
				$command = new Commit();

				if ($job->hasOption('waitsearcher')) {
					$command->setWaitSearcher((bool) $job->getOption('waitsearcher'));
				}

				if ($job->hasOption('softcommit')) {
					$command->setSoftCommit((bool) $job->getOption('softcommit'));
				}

				if ($job->hasOption('expungedeletes')) {
					$command->setExpungeDeletes((bool) $job->getOption('expungedeletes'));
				}

				break;

			case 'ROLLBACK':
				$command = new Rollback();
				break;

			default:
				throw new OutOfBoundsException(sprintf('The jobs action "%s" does not exist, valid actions are "%s"', $job->getAction(), 'ADD, DELETE, OPTIMIZE, ROLLBACK or COMMIT'));
		}

		return $command;
	}

	/**
	 * Send all the operation in the batch to the solr server.
	 *
	 * @throws ClientException when something goes wrong when transmitting the request
	 */
	protected function send()
	{
		$batch = $this->getBatch();

		if ($batch->count() == 0) {
			return; // empty batch
		}

		// Build the query to send to the solr server

		$solr  = $this->getClient();
		$query = $solr->createUpdate();

		foreach ($batch->getIterator() as $operation) {
			$query->add(null, $operation->getCommand());
		}

		$this->getEventDispatcher()->dispatch(Events::SENDING, new SendEvent($this, $query));

		try	{
			$result = $solr->execute($query);
		} catch (Exception $e)	{
			throw new ClientException($e->getMessage(), $e->getCode(), $e);
		}

		$this->getEventDispatcher()->dispatch(Events::RESULTS, new ResultEvent($this, $result));

		// The query is executed so now the messages can be remove
		// from the queue.

		foreach ($batch->getIterator() as $operation) {
			$this->delete($operation->getMessage());
		}

		$batch->clear();
	}

	/**
	 * Delete the queue message from the queue and send a event update
	 * that the message has been processes.
	 *
	 * @param QueueMessageInterface $data
	 */
	protected function delete(QueueMessageInterface $data)
	{
		$this->getQueue()->delete($data);
		$this->getEventDispatcher()->dispatch(Events::PROCESSED, new MessageEvent($this, $data));
	}

	/**
	 * Clean up the indexer.
	 */
	protected function clean()
	{
		if ($this->batch) {
			$this->batch->clear();
		}

		$this->batch = null;
	}
}