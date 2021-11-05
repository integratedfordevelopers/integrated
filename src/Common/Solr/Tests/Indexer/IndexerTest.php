<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr\Tests\Indexer;

use Exception;
use Integrated\Common\Queue\QueueInterface;
use Integrated\Common\Queue\QueueMessageInterface;
use Integrated\Common\Solr\Exception\ClientException;
use Integrated\Common\Solr\Exception\RuntimeException;
use Integrated\Common\Solr\Indexer\Batch;
use Integrated\Common\Solr\Indexer\CommandFactoryInterface;
use Integrated\Common\Solr\Indexer\Event\BatchEvent;
use Integrated\Common\Solr\Indexer\Event\ErrorEvent;
use Integrated\Common\Solr\Indexer\Event\IndexerEvent;
use Integrated\Common\Solr\Indexer\Event\MessageEvent;
use Integrated\Common\Solr\Indexer\Event\ResultEvent;
use Integrated\Common\Solr\Indexer\Event\SendEvent;
use Integrated\Common\Solr\Indexer\Events;
use Integrated\Common\Solr\Indexer\Indexer;
use Integrated\Common\Solr\Indexer\IndexerInterface;
use Integrated\Common\Solr\Indexer\JobInterface;
use Solarium\Core\Client\Client;
use Solarium\Core\Query\Result\ResultInterface;
use Solarium\QueryType\Update\Query\Command\AbstractCommand;
use Solarium\QueryType\Update\Query\Query;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 *
 * @covers \Integrated\Common\Solr\Indexer\Indexer
 */
class IndexerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CommandFactoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $factory;

    /**
     * @var Batch
     */
    private $batch;

    /**
     * @var QueueInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $queue;

    /**
     * @var Client|\PHPUnit_Framework_MockObject_MockObject
     */
    private $client;

    /**
     * @var EventDispatcherInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dispatcher;

    protected function setUp(): void
    {
        $this->factory = $this->createMock(CommandFactoryInterface::class);
//        $this->batch = $this->createMock(Batch::class);
        $this->batch = new Batch();
        $this->queue = $this->createMock(QueueInterface::class);
        $this->client = $this->createMock(Client::class);
        $this->dispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->disableArgumentCloning()->getMock();
    }

    public function testInterface()
    {
        $this->assertInstanceOf(IndexerInterface::class, $this->getInstance());
    }

    public function testConstructor()
    {
        $this->batch = null;

        $instance = $this->getInstance();

        $class = new \ReflectionClass($instance);

        $property = $class->getProperty('batch');
        $property->setAccessible(true);

        self::assertInstanceOf(Batch::class, $property->getValue($instance));
    }

    public function testOptions()
    {
        $instance = $this->getInstance();

        self::assertSame(['queue.size' => 5000, 'batch.size' => 100], $instance->getOptions());

        $instance->setOption('queue.size', 1000);
        $instance->setOption('batch.size', 50);

        self::assertSame(['queue.size' => 1000, 'batch.size' => 50], $instance->getOptions());
    }

    public function testSetGetQueue()
    {
        $this->queue = null;

        $instance = $this->getInstance();

        self::assertInstanceOf(QueueInterface::class, $instance->getQueue());

        $this->queue = $this->createMock(QueueInterface::class);

        $instance->setQueue($this->queue);

        self::assertInstanceOf(QueueInterface::class, $instance->getQueue());
        self::assertSame($this->queue, $instance->getQueue());
    }

    public function testSetGetClient()
    {
        $this->client = null;

        $instance = $this->getInstance();

        self::assertNull($instance->getClient());

        $this->client = $this->createMock(Client::class);

        $instance->setClient($this->client);

        self::assertInstanceOf(Client::class, $instance->getClient());
        self::assertSame($this->client, $instance->getClient());
    }

    public function testSetGetEventDispatcher()
    {
        $this->dispatcher = null;

        $instance = $this->getInstance();

        self::assertInstanceOf(EventDispatcherInterface::class, $instance->getEventDispatcher());

        $this->dispatcher = $this->createMock(EventDispatcherInterface::class);

        $instance->setEventDispatcher($this->dispatcher);

        self::assertInstanceOf(EventDispatcherInterface::class, $instance->getEventDispatcher());
        self::assertSame($this->dispatcher, $instance->getEventDispatcher());
    }

    public function testExecute()
    {
        $instance = $this->getInstance();

        $payload1 = $this->getJob();
        $payload2 = $this->getJob();

        $message1 = $this->getMessage($payload1);
        $message2 = $this->getMessage($payload2);

        $this->queue->expects($this->once())
            ->method('pull')
            ->with($this->equalTo(5000))
            ->willReturn([$message1, $message2]);

        $command1 = $this->getCommand();
        $command2 = $this->getCommand();

        $this->factory->expects($this->exactly(2))
            ->method('create')
            ->withConsecutive([$this->identicalTo($payload1)], [$this->identicalTo($payload2)])
            ->willReturnOnConsecutiveCalls($command1, $command2);

        $query = $this->getQuery();
        $query->expects($this->exactly(2))
            ->method('add')
            ->withConsecutive(
                [$this->equalTo(null), $this->identicalTo($command1)],
                [$this->equalTo(null), $this->identicalTo($command2)]
            );

        $this->client->expects($this->once())
            ->method('createUpdate')
            ->willReturn($query);

        $result = $this->getQueryResult();

        $this->client->expects($this->once())
            ->method('execute')
            ->with($this->identicalTo($query))
            ->willReturn($result);

        $callback = [
            function (IndexerEvent $event) use ($instance) {
                self::assertSame($instance, $event->getIndexer());

                return true;
            },
            function (BatchEvent $event) use ($instance, $message1, $command1) {
                self::assertSame($instance, $event->getIndexer());
                self::assertSame($message1, $event->getOperation()->getMessage());
                self::assertSame($command1, $event->getOperation()->getCommand());

                return true;
            },
            function (BatchEvent $event) use ($instance, $message2, $command2) {
                self::assertSame($instance, $event->getIndexer());
                self::assertSame($message2, $event->getOperation()->getMessage());
                self::assertSame($command2, $event->getOperation()->getCommand());

                return true;
            },
            function (SendEvent $event) use ($instance, $query) {
                self::assertSame($instance, $event->getIndexer());
                self::assertSame($query, $event->getQuery());

                return true;
            },
            function (ResultEvent $event) use ($instance, $result) {
                self::assertSame($instance, $event->getIndexer());
                self::assertSame($result, $event->getResult());

                return true;
            },
            function (MessageEvent $event) use ($instance, $message1) {
                self::assertSame($instance, $event->getIndexer());
                self::assertSame($message1, $event->getMessage());

                return true;
            },
            function (MessageEvent $event) use ($instance, $message2) {
                self::assertSame($instance, $event->getIndexer());
                self::assertSame($message2, $event->getMessage());

                return true;
            },
        ];

        $this->dispatcher->expects($this->exactly(8))
            ->method('dispatch')
            ->withConsecutive(
                [$this->callback($callback[0])],
                [$this->callback($callback[1])],
                [$this->callback($callback[2])],
                [$this->callback($callback[3])],
                [$this->callback($callback[4])],
                [$this->callback($callback[5])],
                [$this->callback($callback[6])],
                [$this->callback($callback[0])]
            )
            ->willReturnArgument(0);

        $instance->execute();

        self::assertEquals(0, $this->batch->count());
    }

    public function testExecuteWithClient()
    {
        $instance = $this->getInstance();

        $payload1 = $this->getJob();
        $payload2 = $this->getJob();

        $message1 = $this->getMessage($payload1);
        $message2 = $this->getMessage($payload2);

        $this->queue->expects($this->once())
            ->method('pull')
            ->with($this->equalTo(5000))
            ->willReturn([$message1, $message2]);

        $command1 = $this->getCommand();
        $command2 = $this->getCommand();

        $this->factory->expects($this->exactly(2))
            ->method('create')
            ->withConsecutive([$this->identicalTo($payload1)], [$this->identicalTo($payload2)])
            ->willReturnOnConsecutiveCalls($command1, $command2);

        $query = $this->getQuery();
        $query->expects($this->exactly(2))
            ->method('add')
            ->withConsecutive(
                [$this->equalTo(null), $this->identicalTo($command1)],
                [$this->equalTo(null), $this->identicalTo($command2)]
            );

        $this->client->expects($this->never())
            ->method($this->anything());

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('createUpdate')
            ->willReturn($query);

        $result = $this->getQueryResult();

        $client->expects($this->once())
            ->method('execute')
            ->with($this->identicalTo($query))
            ->willReturn($result);

        $callback = [
            function (IndexerEvent $event) use ($instance) {
                self::assertSame($instance, $event->getIndexer());

                return true;
            },
            function (BatchEvent $event) use ($instance, $message1, $command1) {
                self::assertSame($instance, $event->getIndexer());
                self::assertSame($message1, $event->getOperation()->getMessage());
                self::assertSame($command1, $event->getOperation()->getCommand());

                return true;
            },
            function (BatchEvent $event) use ($instance, $message2, $command2) {
                self::assertSame($instance, $event->getIndexer());
                self::assertSame($message2, $event->getOperation()->getMessage());
                self::assertSame($command2, $event->getOperation()->getCommand());

                return true;
            },
            function (SendEvent $event) use ($instance, $query) {
                self::assertSame($instance, $event->getIndexer());
                self::assertSame($query, $event->getQuery());

                return true;
            },
            function (ResultEvent $event) use ($instance, $result) {
                self::assertSame($instance, $event->getIndexer());
                self::assertSame($result, $event->getResult());

                return true;
            },
            function (MessageEvent $event) use ($instance, $message1) {
                self::assertSame($instance, $event->getIndexer());
                self::assertSame($message1, $event->getMessage());

                return true;
            },
            function (MessageEvent $event) use ($instance, $message2) {
                self::assertSame($instance, $event->getIndexer());
                self::assertSame($message2, $event->getMessage());

                return true;
            },
        ];

        $this->dispatcher->expects($this->exactly(8))
            ->method('dispatch')
            ->withConsecutive(
                [$this->callback($callback[0])],
                [$this->callback($callback[1])],
                [$this->callback($callback[2])],
                [$this->callback($callback[3])],
                [$this->callback($callback[4])],
                [$this->callback($callback[5])],
                [$this->callback($callback[6])],
                [$this->callback($callback[0])]
            )
            ->willReturnArgument(0);

        $instance->execute($client);

        self::assertEquals(0, $this->batch->count());
        self::assertSame($client, $instance->getClient());
    }

    public function testExecuteNoClient()
    {
        $this->expectException(\Integrated\Common\Solr\Exception\InvalidArgumentException::class);

        $this->client = null;

        $this->getInstance()->execute();
    }

    public function testExecuteEmptyQueue()
    {
        $instance = $this->getInstance();

        $this->queue->expects($this->once())
            ->method('pull')
            ->with($this->equalTo(5000))
            ->willReturn([]);

        $callback = [
            function (IndexerEvent $event) use ($instance) {
                self::assertSame($instance, $event->getIndexer());

                return true;
            },
        ];

        $this->dispatcher->expects($this->exactly(2))
            ->method('dispatch')
            ->withConsecutive(
                [$this->callback($callback[0])],
                [$this->callback($callback[0])]
            )
            ->willReturnArgument(0);

        $instance->execute();

        self::assertEquals(0, $this->batch->count());
    }

    public function testExecuteFactoryReturnsNull()
    {
        $instance = $this->getInstance();

        $payload1 = $this->getJob();
        $payload2 = $this->getJob();

        $message1 = $this->getMessage($payload1);
        $message2 = $this->getMessage($payload2);

        $this->queue->expects($this->once())
            ->method('pull')
            ->with($this->equalTo(5000))
            ->willReturn([$message1, $message2]);

        $this->factory->expects($this->exactly(2))
            ->method('create')
            ->withConsecutive([$this->identicalTo($payload1)], [$this->identicalTo($payload2)])
            ->willReturn(null);

        $this->client->expects($this->never())
            ->method($this->anything());

        $callback = [
            function (IndexerEvent $event) use ($instance) {
                self::assertSame($instance, $event->getIndexer());

                return true;
            },
            function (BatchEvent $event) use ($instance, $message1) {
                self::assertSame($instance, $event->getIndexer());
                self::assertSame($message1, $event->getOperation()->getMessage());
                self::assertNull($event->getOperation()->getCommand());

                return true;
            },
            function (MessageEvent $event) use ($instance, $message1) {
                self::assertSame($instance, $event->getIndexer());
                self::assertSame($message1, $event->getMessage());

                return true;
            },
            function (BatchEvent $event) use ($instance, $message2) {
                self::assertSame($instance, $event->getIndexer());
                self::assertSame($message2, $event->getOperation()->getMessage());
                self::assertNull($event->getOperation()->getCommand());

                return true;
            },
            function (MessageEvent $event) use ($instance, $message2) {
                self::assertSame($instance, $event->getIndexer());
                self::assertSame($message2, $event->getMessage());

                return true;
            },
        ];

        $this->dispatcher->expects($this->exactly(6))
            ->method('dispatch')
            ->withConsecutive(
                [$this->callback($callback[0])],
                [$this->callback($callback[1])],
                [$this->callback($callback[2])],
                [$this->callback($callback[3])],
                [$this->callback($callback[4])],
                [$this->callback($callback[0])]
            )
            ->willReturnArgument(0);

        $instance->execute();

        self::assertEquals(0, $this->batch->count());
    }

    public function testExecuteFactoryError()
    {
        $instance = $this->getInstance();

        $payload1 = $this->getJob();
        $payload2 = $this->getJob();

        $message1 = $this->getMessage($payload1);
        $message2 = $this->getMessage($payload2);

        $this->queue->expects($this->once())
            ->method('pull')
            ->with($this->equalTo(5000))
            ->willReturn([$message1, $message2]);

        $exception = new RuntimeException();

        $this->factory->expects($this->at(0))
            ->method('create')
            ->withConsecutive([$this->identicalTo($payload1)])
            ->willThrowException($exception);

        $command = $this->getCommand();

        $this->factory->expects($this->at(1))
            ->method('create')
            ->withConsecutive([$this->identicalTo($payload2)])
            ->willReturn($command);

        $query = $this->getQuery();
        $query->expects($this->once())
            ->method('add')
            ->with($this->equalTo(null), $this->identicalTo($command));

        $this->client->expects($this->once())
            ->method('createUpdate')
            ->willReturn($query);

        $result = $this->getQueryResult();

        $this->client->expects($this->once())
            ->method('execute')
            ->with($this->identicalTo($query))
            ->willReturn($result);

        $callback = [
            function (IndexerEvent $event) use ($instance) {
                self::assertSame($instance, $event->getIndexer());

                return true;
            },
            function (ErrorEvent $event) use ($instance, $message1, $exception) {
                self::assertSame($instance, $event->getIndexer());
                self::assertSame($message1, $event->getMessage());
                self::assertSame($exception, $event->getException());

                return true;
            },
            function (BatchEvent $event) use ($instance, $message2, $command) {
                self::assertSame($instance, $event->getIndexer());
                self::assertSame($message2, $event->getOperation()->getMessage());
                self::assertSame($command, $event->getOperation()->getCommand());

                return true;
            },
            function (SendEvent $event) use ($instance, $query) {
                self::assertSame($instance, $event->getIndexer());
                self::assertSame($query, $event->getQuery());

                return true;
            },
            function (ResultEvent $event) use ($instance, $result) {
                self::assertSame($instance, $event->getIndexer());
                self::assertSame($result, $event->getResult());

                return true;
            },
            function (MessageEvent $event) use ($instance, $message2) {
                self::assertSame($instance, $event->getIndexer());
                self::assertSame($message2, $event->getMessage());

                return true;
            },
        ];

        $this->dispatcher->expects($this->exactly(7))
            ->method('dispatch')
            ->withConsecutive(
                [$this->callback($callback[0])],
                [$this->callback($callback[1])],
                [$this->callback($callback[2])],
                [$this->callback($callback[3])],
                [$this->callback($callback[4])],
                [$this->callback($callback[5])],
                [$this->callback($callback[0])]
            )
            ->willReturnArgument(0);

        $instance->execute();

        self::assertEquals(0, $this->batch->count());
    }

    public function testExecuteOperationModification()
    {
        $instance = $this->getInstance();

        $payload1 = $this->getJob();
        $payload2 = $this->getJob();

        $message1 = $this->getMessage($payload1);
        $message2 = $this->getMessage($payload2);

        $this->queue->expects($this->once())
            ->method('pull')
            ->with($this->equalTo(5000))
            ->willReturn([$message1, $message2]);

        $command1 = $this->getCommand();
        $command2 = $this->getCommand();
        $command3 = $this->getCommand();

        $this->factory->expects($this->exactly(2))
            ->method('create')
            ->withConsecutive([$this->identicalTo($payload1)], [$this->identicalTo($payload2)])
            ->willReturnOnConsecutiveCalls($command1, $command2);

        $query = $this->getQuery();
        $query->expects($this->once())
            ->method('add')
            ->with($this->equalTo(null), $this->identicalTo($command3));

        $this->client->expects($this->once())
            ->method('createUpdate')
            ->willReturn($query);

        $result = $this->getQueryResult();

        $this->client->expects($this->once())
            ->method('execute')
            ->with($this->identicalTo($query))
            ->willReturn($result);

        $callback = [
            function (IndexerEvent $event) use ($instance) {
                self::assertSame($instance, $event->getIndexer());

                return true;
            },
            function (BatchEvent $event) use ($instance, $message1, $command3) {
                self::assertSame($instance, $event->getIndexer());
                self::assertSame($message1, $event->getOperation()->getMessage());

                $event->getOperation()->setCommand($command3);

                return true;
            },
            function (BatchEvent $event) use ($instance, $message2) {
                self::assertSame($instance, $event->getIndexer());
                self::assertSame($message2, $event->getOperation()->getMessage());

                $event->getOperation()->setCommand(null);

                return true;
            },
            function (MessageEvent $event) use ($instance, $message2) {
                self::assertSame($instance, $event->getIndexer());
                self::assertSame($message2, $event->getMessage());

                return true;
            },
            function (SendEvent $event) use ($instance, $query) {
                self::assertSame($instance, $event->getIndexer());
                self::assertSame($query, $event->getQuery());

                return true;
            },
            function (ResultEvent $event) use ($instance, $result) {
                self::assertSame($instance, $event->getIndexer());
                self::assertSame($result, $event->getResult());

                return true;
            },
            function (MessageEvent $event) use ($instance, $message1) {
                self::assertSame($instance, $event->getIndexer());
                self::assertSame($message1, $event->getMessage());

                return true;
            },
        ];

        $this->dispatcher->expects($this->exactly(8))
            ->method('dispatch')
            ->withConsecutive(
                [$this->callback($callback[0])],
                [$this->callback($callback[1])],
                [$this->callback($callback[2])],
                [$this->callback($callback[3])],
                [$this->callback($callback[4])],
                [$this->callback($callback[5])],
                [$this->callback($callback[6])],
                [$this->callback($callback[0])]
            )
            ->willReturnArgument(0);

        $instance->execute();

        self::assertEquals(0, $this->batch->count());
    }

    public function testExecuteClientError()
    {
        $instance = $this->getInstance();

        $payload1 = $this->getJob();
        $payload2 = $this->getJob();

        $message1 = $this->getMessage($payload1, false);
        $message2 = $this->getMessage($payload2, false);

        $this->queue->expects($this->once())
            ->method('pull')
            ->with($this->equalTo(5000))
            ->willReturn([$message1, $message2]);

        $command1 = $this->getCommand();
        $command2 = $this->getCommand();

        $this->factory->expects($this->exactly(2))
            ->method('create')
            ->withConsecutive([$this->identicalTo($payload1)], [$this->identicalTo($payload2)])
            ->willReturnOnConsecutiveCalls($command1, $command2);

        $query = $this->getQuery();
        $query->expects($this->exactly(2))
            ->method('add')
            ->withConsecutive(
                [$this->equalTo(null), $this->identicalTo($command1)],
                [$this->equalTo(null), $this->identicalTo($command2)]
            );

        $this->client->expects($this->once())
            ->method('createUpdate')
            ->willReturn($query);

        $exception = new Exception();

        $this->client->expects($this->once())
            ->method('execute')
            ->willThrowException($exception);

        $callback = [
            function (IndexerEvent $event) use ($instance) {
                self::assertSame($instance, $event->getIndexer());

                return true;
            },
            function (BatchEvent $event) use ($instance, $message1, $command1) {
                self::assertSame($instance, $event->getIndexer());
                self::assertSame($message1, $event->getOperation()->getMessage());
                self::assertSame($command1, $event->getOperation()->getCommand());

                return true;
            },
            function (BatchEvent $event) use ($instance, $message2, $command2) {
                self::assertSame($instance, $event->getIndexer());
                self::assertSame($message2, $event->getOperation()->getMessage());
                self::assertSame($command2, $event->getOperation()->getCommand());

                return true;
            },
            function (SendEvent $event) use ($instance, $query) {
                self::assertSame($instance, $event->getIndexer());
                self::assertSame($query, $event->getQuery());

                return true;
            },
        ];

        $this->dispatcher->expects($this->exactly(5))
            ->method('dispatch')
            ->withConsecutive(
                [$this->callback($callback[0])],
                [$this->callback($callback[1])],
                [$this->callback($callback[2])],
                [$this->callback($callback[3])],
                [$this->callback($callback[0])]
            )
            ->willReturnArgument(0);

        try {
            $instance->execute();
        } catch (ClientException $ex) {
            self::assertSame($exception, $ex->getPrevious());
        }

        self::assertEquals(0, $this->batch->count());
    }

    public function testExecuteQueueSize()
    {
        $instance = $this->getInstance();
        $instance->setOption('queue.size', 1000);

        $this->queue->expects($this->once())
            ->method('pull')
            ->with($this->equalTo(1000))
            ->willReturn([]);

        $instance->execute();

        self::assertEquals(0, $this->batch->count());
    }

    public function testExecuteBatchSize()
    {
        $instance = $this->getInstance();
        $instance->setOption('batch.size', 1);

        $payload1 = $this->getJob();
        $payload2 = $this->getJob();

        $message1 = $this->getMessage($payload1);
        $message2 = $this->getMessage($payload2);

        $this->queue->expects($this->once())
            ->method('pull')
            ->with($this->equalTo(5000))
            ->willReturn([$message1, $message2]);

        $command1 = $this->getCommand();
        $command2 = $this->getCommand();

        $this->factory->expects($this->exactly(2))
            ->method('create')
            ->withConsecutive([$this->identicalTo($payload1)], [$this->identicalTo($payload2)])
            ->willReturnOnConsecutiveCalls($command1, $command2);

        $query1 = $this->getQuery();
        $query1->expects($this->once())
            ->method('add')
            ->with($this->equalTo(null), $this->identicalTo($command1));

        $query2 = $this->getQuery();
        $query2->expects($this->once())
            ->method('add')
            ->with($this->equalTo(null), $this->identicalTo($command2));

        $this->client->expects($this->exactly(2))
            ->method('createUpdate')
            ->willReturnOnConsecutiveCalls($query1, $query2);

        $result1 = $this->getQueryResult();
        $result2 = $this->getQueryResult();

        $this->client->expects($this->exactly(2))
            ->method('execute')
            ->withConsecutive([$this->identicalTo($query1)], [$this->identicalTo($query2)])
            ->willReturnOnConsecutiveCalls($result1, $result2);

        $callback = [
            function (IndexerEvent $event) use ($instance) {
                self::assertSame($instance, $event->getIndexer());

                return true;
            },
            function (BatchEvent $event) use ($instance, $message1, $command1) {
                self::assertSame($instance, $event->getIndexer());
                self::assertSame($message1, $event->getOperation()->getMessage());
                self::assertSame($command1, $event->getOperation()->getCommand());

                return true;
            },
            function (SendEvent $event) use ($instance, $query1) {
                self::assertSame($instance, $event->getIndexer());
                self::assertSame($query1, $event->getQuery());

                return true;
            },
            function (ResultEvent $event) use ($instance, $result1) {
                self::assertSame($instance, $event->getIndexer());
                self::assertSame($result1, $event->getResult());

                return true;
            },
            function (MessageEvent $event) use ($instance, $message1) {
                self::assertSame($instance, $event->getIndexer());
                self::assertSame($message1, $event->getMessage());

                return true;
            },
            function (BatchEvent $event) use ($instance, $message2, $command2) {
                self::assertSame($instance, $event->getIndexer());
                self::assertSame($message2, $event->getOperation()->getMessage());
                self::assertSame($command2, $event->getOperation()->getCommand());

                return true;
            },
            function (SendEvent $event) use ($instance, $query2) {
                self::assertSame($instance, $event->getIndexer());
                self::assertSame($query2, $event->getQuery());

                return true;
            },
            function (ResultEvent $event) use ($instance, $result2) {
                self::assertSame($instance, $event->getIndexer());
                self::assertSame($result2, $event->getResult());

                return true;
            },
            function (MessageEvent $event) use ($instance, $message2) {
                self::assertSame($instance, $event->getIndexer());
                self::assertSame($message2, $event->getMessage());

                return true;
            },
        ];

        $this->dispatcher->expects($this->exactly(10))
            ->method('dispatch')
            ->withConsecutive(
                [$this->callback($callback[0])],
                [$this->callback($callback[1])],
                [$this->callback($callback[2])],
                [$this->callback($callback[3])],
                [$this->callback($callback[4])],
                [$this->callback($callback[5])],
                [$this->callback($callback[6])],
                [$this->callback($callback[7])],
                [$this->callback($callback[8])],
                [$this->callback($callback[0])]
            )
            ->willReturnArgument(0);

        $instance->execute();

        self::assertEquals(0, $this->batch->count());
    }

    /**
     * @return Indexer
     */
    protected function getInstance()
    {
        $instance = new Indexer($this->factory, $this->batch);

        if ($this->queue) {
            $instance->setQueue($this->queue);
        }

        if ($this->client) {
            $instance->setClient($this->client);
        }

        if ($this->dispatcher) {
            $instance->setEventDispatcher($this->dispatcher);
        }

        return $instance;
    }

    /**
     * @return JobInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getJob()
    {
        return $this->createMock(JobInterface::class);
    }

    /**
     * @return AbstractCommand|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getCommand()
    {
        return $this->createMock(AbstractCommand::class);
    }

    /**
     * @param mixed $payload
     * @param bool  $delete
     *
     * @return QueueMessageInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getMessage($payload, $delete = true)
    {
        $mock = $this->createMock(QueueMessageInterface::class);
        $mock->expects($this->any())
            ->method('getPayload')
            ->willReturn($payload);

        $mock->expects($delete ? $this->atLeastOnce() : $this->never())
            ->method('delete');

        return $mock;
    }

    /**
     * @return Query|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getQuery()
    {
        return $this->createMock(Query::class);
    }

    /**
     * @return ResultInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getQueryResult()
    {
        return $this->createMock(ResultInterface::class);
    }
}
