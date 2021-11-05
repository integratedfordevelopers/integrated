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
use Integrated\Common\Queue\Provider\Memory\QueueProvider;
use Integrated\Common\Queue\Queue;
use Integrated\Common\Queue\QueueInterface;
use Integrated\Common\Queue\QueueMessageInterface;
use Integrated\Common\Solr\Configurable;
use Integrated\Common\Solr\Exception\ClientException;
use Integrated\Common\Solr\Exception\InvalidArgumentException;
use Integrated\Common\Solr\Exception\RuntimeException;
use Integrated\Common\Solr\Indexer\Event\BatchEvent;
use Integrated\Common\Solr\Indexer\Event\ErrorEvent;
use Integrated\Common\Solr\Indexer\Event\IndexerEvent;
use Integrated\Common\Solr\Indexer\Event\MessageEvent;
use Integrated\Common\Solr\Indexer\Event\ResultEvent;
use Integrated\Common\Solr\Indexer\Event\SendEvent;
use Solarium\Core\Client\Client;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Indexer extends Configurable implements IndexerInterface
{
    /**
     * @var QueueInterface
     */
    private $queue = null;

    /**
     * @var Client
     */
    private $client = null;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher = null;

    /**
     * @var CommandFactoryInterface
     */
    protected $factory;

    /**
     * @var Batch
     */
    protected $batch;

    /**
     * Indexer constructor.
     *
     * @param CommandFactoryInterface $factory
     * @param Batch                   $batch
     */
    public function __construct(CommandFactoryInterface $factory, Batch $batch = null)
    {
        parent::__construct();

        $this->factory = $factory;
        $this->batch = $batch ?: new Batch();
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'queue.size' => 5000,
            'batch.size' => 100,
        ]);

        $resolver
            ->setAllowedTypes('queue.size', 'integer')
            ->setAllowedTypes('batch.size', 'integer')
        ;
    }

    /**
     * Set the event dispatcher.
     *
     * @param EventDispatcherInterface $dispatcher
     */
    public function setEventDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
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
     * {@inheritdoc}
     */
    public function setQueue(QueueInterface $queue)
    {
        $this->queue = $queue;
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
     * {@inheritdoc}
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get the solarium client.
     *
     * @return Client|null
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Client $client = null)
    {
        if ($client !== null) {
            $this->setClient($client);
        }

        if ($this->getClient() === null) {
            throw new InvalidArgumentException('No instance of a Solarium\Core\Client\Client has been inserted into the indexer.');
        }

        $this->getEventDispatcher()->dispatch(new IndexerEvent($this), Events::PRE_EXECUTE);

        try {
            foreach ($this->getQueue()->pull($this->getOption('queue.size')) as $message) {
                $this->batch($message);
            }

            $this->send(); // send the last batch if there is any
        } finally {
            $this->batch->clear();
            $this->getEventDispatcher()->dispatch(new IndexerEvent($this), Events::POST_EXECUTE);
        }
    }

    /**
     * A queue message it not send to the solr server but grouped in a
     * batch to send more operations at ones.
     *
     * @param QueueMessageInterface $message
     */
    protected function batch(QueueMessageInterface $message)
    {
        try {
            $operation = new BatchOperation($message, $this->factory->create($message->getPayload()));
        } catch (RuntimeException $e) {
            $event = $this->getEventDispatcher()->dispatch(new ErrorEvent($this, $message, $e), Events::ERROR);
            $event->getMessage()->delete();

            return;
        }

        // Send a event to allow the batch operation to be changed by external code. After
        // that check if the batch is canceled or not. If canceled just remove the message
        // from the queue and drop the batch operation

        $this->getEventDispatcher()->dispatch(new BatchEvent($this, $operation), Events::BATCHING);

        if ($operation->getCommand() === null) {
            $event = $this->getEventDispatcher()->dispatch(new MessageEvent($this, $message), Events::PROCESSED);
            $event->getMessage()->delete();

            return;
        }

        $this->batch->add($operation);

        if ($this->batch->count() >= $this->getOption('batch.size')) {
            $this->send();
        }
    }

    /**
     * Send all the operation in the batch to the solr server.
     *
     * @throws ClientException when something goes wrong when transmitting the request
     */
    protected function send()
    {
        if ($this->batch->count() == 0) {
            return; // empty batch
        }

        $query = $this->getClient()->createUpdate();

        /** @var BatchOperation $operation */
        foreach ($this->batch as $operation) {
            $query->add(null, $operation->getCommand());
        }

        $dispatcher = $this->getEventDispatcher();
        $dispatcher->dispatch(new SendEvent($this, $query), Events::SENDING);

        try {
            $result = $this->getClient()->execute($query);
        } catch (Exception $e) {
            throw new ClientException($e->getMessage(), $e->getCode(), $e);
        }

        $dispatcher->dispatch(new ResultEvent($this, $result), Events::RESULTS);

        /** @var BatchOperation $operation */
        foreach ($this->batch as $operation) {
            $event = $dispatcher->dispatch(new MessageEvent($this, $operation->getMessage()), Events::PROCESSED);
            $event->getMessage()->delete();
        }

        $this->batch->clear();
    }
}
