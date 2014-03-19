<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\Solr\Indexer;

use Integrated\Common\Solr\Converter\ConverterInterface;

use Integrated\Common\Solr\Indexer\Batch;
use Integrated\Common\Solr\Indexer\BatchOperation;
use Integrated\Common\Solr\Indexer\Event\BatchEvent;
use Integrated\Common\Solr\Indexer\Indexer;
use Integrated\Common\Solr\Indexer\Events;
use Integrated\Common\Solr\Indexer\JobInterface;

use Integrated\Common\Queue\QueueInterface;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Serializer\SerializerInterface;

use Solarium\Core\Client\Client;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 *
 * @covers Integrated\Common\Solr\Indexer\Indexer
 * @coversDefaultClass Integrated\Common\Solr\Indexer\Indexer
 */
class IndexerTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var EventDispatcherInterface | \PHPUnit_Framework_MockObject_MockObject
	 */
	protected $dispatcher;

	/**
	 * @var QueueInterface | \PHPUnit_Framework_MockObject_MockObject
	 */
	protected $queue;

	/**
	 * @var SerializerInterface | \PHPUnit_Framework_MockObject_MockObject
	 */
	protected $serializer;

	/**
	 * @var ConverterInterface | \PHPUnit_Framework_MockObject_MockObject
	 */
	protected $converter;

	/**
	 * @var Client | \PHPUnit_Framework_MockObject_MockObject
	 */
	protected $client;

	/**
	 * @var Indexer | \PHPUnit_Framework_MockObject_MockObject
	 */
	protected $indexer;

	protected function setUp()
	{
		$this->dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
		$this->queue = $this->getMock('Integrated\Common\Queue\QueueInterface');
		$this->serializer = $this->getMock('Symfony\Component\Serializer\SerializerInterface');
		$this->converter = $this->getMock('Integrated\Common\Solr\Converter\ConverterInterface');
		$this->client = $this->getMock('Solarium\Core\Client\Client');
	}

	public function testInterface()
	{
		$this->assertInstanceOf('Integrated\Common\Solr\Indexer\IndexerInterface', $this->getIndexer());
	}

	public function testSetAndGetEventDispatcher()
	{
		$this->indexer = $this->getIndexer();
		$this->indexer->setEventDispatcher($this->dispatcher);

		$this->assertSame($this->dispatcher, $this->indexer->getEventDispatcher());
	}

	public function testGetDefaultEventDispatcher()
	{
		$this->indexer = $this->getIndexer();

		$temp = $this->indexer->getEventDispatcher();

		$this->assertInstanceOf('Symfony\Component\EventDispatcher\EventDispatcher', $temp);
		$this->assertSame($temp, $this->indexer->getEventDispatcher());
	}

	public function testSetAndGetQueue()
	{
		$this->indexer = $this->getIndexer();
		$this->indexer->setQueue($this->queue);

		$this->assertSame($this->queue, $this->indexer->getQueue());
	}

	public function testGetDefaultQueue()
	{
		$this->indexer = $this->getIndexer();

		$temp = $this->indexer->getQueue();

		$this->assertInstanceOf('Integrated\Common\Queue\Queue', $temp);
		$this->assertSame($temp, $this->indexer->getQueue());
	}

	public function testSetandGetSerializer()
	{
		$this->indexer = $this->getIndexer();
		$this->indexer->setSerializer($this->serializer);

		$this->assertSame($this->serializer, $this->indexer->getSerializer());
	}

	public function testGetDefaultSerializer()
	{
		$this->indexer = $this->getIndexer();

		$temp = $this->indexer->getSerializer();

		$this->assertInstanceOf('Symfony\Component\Serializer\Serializer', $temp);
		$this->assertSame($temp, $this->indexer->getSerializer());
	}

	public function testSetAndGetConverter()
	{
		$this->indexer = $this->getIndexer();
		$this->indexer->setConverter($this->converter);

		$this->assertSame($this->converter, $this->indexer->getConverter());
	}

	public function testGetDefaultConverter()
	{
		$this->indexer = $this->getIndexer();

		$temp = $this->indexer->getConverter();

		$this->assertInstanceOf('Integrated\Common\Solr\Converter\Converter', $temp);
		$this->assertSame($temp, $this->indexer->getConverter());
	}

	public function testSetAndGetClient()
	{
		$this->indexer = $this->getIndexer();
		$this->indexer->setClient($this->client);

		$this->assertSame($this->client, $this->indexer->getClient());
	}

	public function testGetDefaultClient()
	{
		$this->assertNull($this->getIndexer()->getClient());
	}

	public function testGetBatch()
	{
		$this->indexer = $this->getIndexer();

		$temp = $this->invoke($this->indexer, 'getBatch');

		$this->assertInstanceOf('Integrated\Common\Solr\Indexer\Batch', $temp);
		$this->assertSame($temp, $this->invoke($this->indexer, 'getBatch'));
	}

	public function testExecute()
	{
		$this->dispatcher->expects($this->exactly(2))->method('dispatch');

		$this->queue->expects($this->once())->method('pull')->with($this->identicalTo(1000))->will($this->returnValue([]));

		$this->indexer = $this->getIndexer(['batch'], true);
		$this->indexer->expects($this->never())->method('batch');

		$this->indexer->execute();
	}

	public function testExecuteWithClient()
	{
		$this->indexer = $this->getIndexer();
		$this->indexer->execute($this->client);

		$this->assertSame($this->client, $this->indexer->getClient());
	}

	/**
	 * @expectedException \Integrated\Common\Solr\Exception\InvalidArgumentException
	 */
	public function testExecuteNoClient()
	{
		$this->indexer = $this->getIndexer();
		$this->indexer->execute();
	}

	public function testExecuteEventDispatch()
	{
		// test if the event dispatcher is called in the right order and with
		// the correct arguments.

		$callback = function($value) {
			return $value instanceof \Integrated\Common\Solr\Indexer\Event\IndexerEvent && $value->getIndexer() === $this->indexer;
		};

		$this->dispatcher->expects($this->at(0))->method('dispatch')->with($this->identicalTo(Events::PRE_EXECUTE), $this->callback($callback));
		$this->dispatcher->expects($this->at(1))->method('dispatch')->with($this->identicalTo(Events::POST_EXECUTE), $this->callback($callback));

		$this->queue->expects($this->once())->method('pull')->will($this->returnValue([]));

		$this->indexer = $this->getIndexer([], true);
		$this->indexer->execute();
	}

	public function testExecuteBatchCalls()
	{
		// test that with a queue size of 5 message that the batch command is also
		// called 5 times. After everything is done send need to be called atleast
		// once.

		$message = $this->getMock('Integrated\Common\Queue\QueueMessageInterface');

		$this->queue->expects($this->once())->method('pull')->will($this->returnValue([$message, $message, $message, $message, $message]));

		$this->indexer = $this->getIndexer(['batch', 'send', 'clean'], true);

		$this->indexer->expects($this->exactly(5))->method('batch')->with($this->identicalTo($message));
		$this->indexer->expects($this->atLeastOnce())->method('send');
		$this->indexer->expects($this->atLeastOnce())->method('clean');

		$this->indexer->execute();
	}

	/**
	 * @expectedException \Integrated\Common\Solr\Exception\ExceptionInterface
	 */
	public function testExecuteBatchException()
	{
		// test that after a error occurs the the indexer handles it gracefully and
		// cleans it self up.

		$this->queue->expects($this->once())->method('pull')->will($this->returnValue([$this->getMock('Integrated\Common\Queue\QueueMessageInterface')]));

		$this->dispatcher->expects($this->exactly(2))->method('dispatch');

		$this->indexer = $this->getIndexer(['batch', 'send', 'clean'], true);

		$this->indexer->expects($this->any())->method('batch')->will($this->throwException($this->getMock('Integrated\Common\Solr\Exception\RuntimeException')));
		$this->indexer->expects($this->never())->method('send');
		$this->indexer->expects($this->atLeastOnce())->method('clean');

		$this->indexer->execute();
	}

	/**
	 * @expectedException \Integrated\Common\Solr\Exception\RuntimeException
	 */
	public function testExecuteBatchExceptionEventDispatch()
	{
		// test if the event dispatcher is called in the right order and with
		// the correct arguments after a exception. This only test the error
		// event handling. For the event test on PRE_EXECUTE and POST_EXECUTE
		// see the test testExecuteEventDispatch.

		$exception = $this->getMock('Integrated\Common\Solr\Exception\RuntimeException');

		$callback = function($value) use ($exception) {
			return $value instanceof \Integrated\Common\Solr\Indexer\Event\ErrorEvent && $value->getIndexer() === $this->indexer && $value->getException() === $exception;
		};

		$this->queue->expects($this->once())->method('pull')->will($this->returnValue([$this->getMock('Integrated\Common\Queue\QueueMessageInterface')]));

		$this->dispatcher->expects($this->at(0))->method('dispatch')->with($this->identicalTo(Events::PRE_EXECUTE));
		$this->dispatcher->expects($this->at(1))->method('dispatch')->with($this->identicalTo(Events::ERROR), $this->callback($callback));

		$this->indexer = $this->getIndexer(['batch', 'send', 'clean'], true);
		$this->indexer->expects($this->any())->method('batch')->will($this->throwException($exception));

		$this->indexer->execute();
	}

	public function testBatch()
	{
		$message = $this->getMock('Integrated\Common\Queue\QueueMessageInterface');
		$message->expects($this->once())->method('getPayload')->will($this->returnValue($this->getMock('Integrated\Common\Solr\Indexer\JobInterface')));

		$this->dispatcher->expects($this->once())->method('dispatch');

		$this->indexer = $this->getIndexer(['convert'], true);
		$this->indexer->expects($this->once())->method('convert')->will($this->returnValue($this->getMock('Solarium\QueryType\Update\Query\Command\Command')));

		$this->invoke($this->indexer, 'batch', $message);

		$this->assertEquals(1, $this->invoke($this->indexer, 'getBatch')->count());
	}

	public function testBatchEventDispatch()
	{
		// test if the event dispatcher is called in the right order and with
		// the correct arguments. The message should be the same as the given
		// to the batch method and the command should be the same as returned
		// by de convert mock.

		$message = $this->getMock('Integrated\Common\Queue\QueueMessageInterface');
		$message->expects($this->once())->method('getPayload')->will($this->returnValue($this->getMock('Integrated\Common\Solr\Indexer\JobInterface')));

		$command = $this->getMock('Solarium\QueryType\Update\Query\Command\Command');

		$callback = function($value) use ($message, $command) {
			return $value instanceof \Integrated\Common\Solr\Indexer\Event\BatchEvent && $value->getIndexer() === $this->indexer
				&& $value->getOperation() !== null && $value->getOperation()->getMessage() === $message && $value->getOperation()->getCommand() === $command;
		};

		$this->dispatcher->expects($this->once())->method('dispatch')->with($this->identicalTo(Events::BATCHING), $this->callback($callback));

		$this->indexer = $this->getIndexer(['convert'], true);
		$this->indexer->expects($this->once())->method('convert')->will($this->returnValue($command));

		$this->invoke($this->indexer, 'batch', $message);
	}

	public function testBatchEventDispatchCantConvert()
	{
		// test if the event dispatcher is called with a BatchEvent with
		// null as command. there should not be a error generated when the
		// converter returns null.

		$message = $this->getMock('Integrated\Common\Queue\QueueMessageInterface');
		$message->expects($this->once())->method('getPayload')->will($this->returnValue($this->getMock('Integrated\Common\Solr\Indexer\JobInterface')));

		$callback = function($value) use ($message) {
			return $value instanceof \Integrated\Common\Solr\Indexer\Event\BatchEvent && $value->getIndexer() === $this->indexer
				&& $value->getOperation() !== null && $value->getOperation()->getMessage() === $message && $value->getOperation()->getCommand() === null;
		};

		$this->dispatcher->expects($this->once())->method('dispatch')->with($this->identicalTo(Events::BATCHING), $this->callback($callback));

		$this->indexer = $this->getIndexer(['convert', 'delete'], true);

		$this->indexer->expects($this->once())->method('convert')->will($this->returnValue(null));
		$this->indexer->expects($this->once())->method('delete')->with($this->identicalTo($message));

		$this->invoke($this->indexer, 'batch', $message);
	}

	public function testBatchCommandUnset()
	{
		// test if the message is delete and not batched if the command is
		// unset (set to null) after the event is dispatched.

		$message = $this->getMock('Integrated\Common\Queue\QueueMessageInterface');
		$message->expects($this->once())->method('getPayload')->will($this->returnValue($this->getMock('Integrated\Common\Solr\Indexer\JobInterface')));

		$callback = function($ignore, $value) {
			/** @var BatchEvent $value */
			$value->getOperation()->setCommand(null);
		};

		$this->dispatcher->expects($this->once())->method('dispatch')->with($this->identicalTo(Events::BATCHING))->will($this->returnCallback($callback));

		$this->indexer = $this->getIndexer(['convert', 'delete', 'send'], true);

		$this->indexer->expects($this->once())->method('convert')->will($this->returnValue($this->getMock('Solarium\QueryType\Update\Query\Command\Command')));
		$this->indexer->expects($this->once())->method('delete')->with($this->identicalTo($message));
		$this->indexer->expects($this->never())->method('send');

		$this->invoke($this->indexer, 'batch', $message);

		$this->assertEquals(0, $this->invoke($this->indexer, 'getBatch')->count());
	}

	public function testBatchCommandChange()
	{
		// test if the command that is changed while the operation was dispatch
		// as a event is actually put in the batch.

		$message = $this->getMock('Integrated\Common\Queue\QueueMessageInterface');
		$message->expects($this->once())->method('getPayload')->will($this->returnValue($this->getMock('Integrated\Common\Solr\Indexer\JobInterface')));

		$command = $this->getMock('Solarium\QueryType\Update\Query\Command\Command');

		$callback = function($ignore, $value) use ($command) {
			/** @var BatchEvent $value */
			$value->getOperation()->setCommand($command);
		};

		$this->dispatcher->expects($this->once())->method('dispatch')->with($this->identicalTo(Events::BATCHING))->will($this->returnCallback($callback));

		$this->indexer = $this->getIndexer(['convert', 'delete'], true);

		$this->indexer->expects($this->once())->method('convert')->will($this->returnValue(null));
		$this->indexer->expects($this->never())->method('delete');

		$this->invoke($this->indexer, 'batch', $message);

		/** @var BatchOperation $operation  */
		$operation = iterator_to_array($this->invoke($this->indexer, 'getBatch'));
		$operation = $operation[0];

		$this->assertNotNull($operation->getCommand());
		$this->assertSame($command, $operation->getCommand());
	}

	public function testBatchCommandImmutable()
	{
		// after the operation is added to the batch it should not be possible anymore
		// to unset the command from the operation when for some reason some one stored
		// the object after its dispatched.

		$message = $this->getMock('Integrated\Common\Queue\QueueMessageInterface');
		$message->expects($this->once())->method('getPayload')->will($this->returnValue($this->getMock('Integrated\Common\Solr\Indexer\JobInterface')));

		/** @var BatchOperation $operation  */
		$operation = null;

		$callback = function($ignore, $value) use (&$operation) {
			/** @var BatchEvent $value */
			$operation = $value->getOperation();
		};

		$this->dispatcher->expects($this->once())->method('dispatch')->with($this->identicalTo(Events::BATCHING))->will($this->returnCallback($callback));

		$this->indexer = $this->getIndexer(['convert', 'delete', 'send'], true);

		$this->indexer->expects($this->once())->method('convert')->will($this->returnValue($this->getMock('Solarium\QueryType\Update\Query\Command\Command')));
		$this->indexer->expects($this->never())->method('delete');

		$this->invoke($this->indexer, 'batch', $message);

		$operation->setCommand(null);

		$operation = iterator_to_array($this->invoke($this->indexer, 'getBatch'));
		$operation = $operation[0];

		$this->assertNotNull($operation->getCommand());
		$this->assertInstanceOf('Solarium\QueryType\Update\Query\Command\Command', $operation->getCommand());
	}

	public function testBatchSize()
	{
		// test if the batch is send after 10 calls

		$message = $this->getMock('Integrated\Common\Queue\QueueMessageInterface');
		$message->expects($this->any())->method('getPayload')->will($this->returnValue($this->getMock('Integrated\Common\Solr\Indexer\JobInterface')));

		$this->indexer = $this->getIndexer(['convert', 'delete', 'send'], true);

		$this->indexer->expects($this->any())->method('convert')->will($this->returnValue($this->getMock('Solarium\QueryType\Update\Query\Command\Command')));
		$this->indexer->expects($this->never())->method('delete');
		$this->indexer->expects($this->once())->method('send');

		for ($i = 0; $i < 10; $i++) {
			$this->invoke($this->indexer, 'batch', $message);
		}

		$this->assertEquals(10, $this->invoke($this->indexer, 'getBatch')->count());
	}

	/**
	 * @expectedException \Integrated\Common\Solr\Exception\OutOfBoundsException
	 */
	public function testConvertNoAction()
	{
		$this->indexer = $this->getIndexer();
		$this->invoke($this->indexer, 'convert', $this->getJob());
	}

	/**
	 * @expectedException \Integrated\Common\Solr\Exception\OutOfBoundsException
	 */
	public function testConvertInvalidAction()
	{
		$this->indexer = $this->getIndexer();
		$this->invoke($this->indexer, 'convert', $this->getJob('invalid'));
	}

	public function testConvertAdd()
	{
		$document = new \stdClass();

		$this->serializer->expects($this->once())
			->method('deserialize')
			->with($this->identicalTo('data'), $this->identicalTo('class'), $this->identicalTo('format'))
			->will($this->returnValue($document));

		$this->converter->expects($this->once())->method('getFields')->with($this->identicalTo($document))->will($this->returnValue(['key' => 'value']));

		$this->indexer = $this->getIndexer(null, true);

		$command = $this->invoke($this->indexer, 'convert', $this->getJob('add', ['document.data' => 'data', 'document.class' => 'class', 'document.format' => 'format']));

		$this->assertInstanceOf('Solarium\QueryType\Update\Query\Command\Add', $command);
		$this->assertEmpty($command->getOptions());
		$this->assertCount(1, $command->getDocuments());
		$this->assertEquals(['key' => 'value'], $command->getDocuments()[0]->getFields());
	}

	public function testConvertAddWithOption()
	{
		$this->serializer->expects($this->once())->method('deserialize')->will($this->returnValue(new \stdClass()));
		$this->converter->expects($this->once())->method('getFields')->will($this->returnValue(['key' => 'value']));

		$this->indexer = $this->getIndexer(null, true);

		$options = ['overwrite' => true, 'commitwithin' => false];

		$command = $this->invoke($this->indexer, 'convert', $this->getJob('add', $options + ['document.data' => 'data', 'document.class' => 'class', 'document.format' => 'format']));

		$this->assertInstanceOf('Solarium\QueryType\Update\Query\Command\Add', $command);
		$this->assertEquals(['overwrite' => true, 'commitwithin' => false], $command->getOptions());
		$this->assertCount(1, $command->getDocuments());
	}

	public function testConvertAddWithOptionNoneBool()
	{
		$this->serializer->expects($this->once())->method('deserialize')->will($this->returnValue(new \stdClass()));
		$this->converter->expects($this->once())->method('getFields')->will($this->returnValue(['key' => 'value']));

		$this->indexer = $this->getIndexer(null, true);

		$options = ['overwrite' => 'yes', 'commitwithin' => null];

		$command = $this->invoke($this->indexer, 'convert', $this->getJob('add', $options + ['document.data' => 'data', 'document.class' => 'class', 'document.format' => 'format']));

		$this->assertInstanceOf('Solarium\QueryType\Update\Query\Command\Add', $command);
		$this->assertEquals(['overwrite' => true, 'commitwithin' => false], $command->getOptions());
		$this->assertCount(1, $command->getDocuments());
	}

	public function testConvertAddMissingDeserializeData()
	{
		$this->indexer = $this->getIndexer();
		$this->assertNull($this->invoke($this->indexer, 'convert', $this->getJob('add')));
	}

	public function testConvertAddNoDeserialize()
	{
		$this->serializer->expects($this->once())->method('deserialize')->will($this->returnValue(null));

		$this->indexer = $this->getIndexer(null, true);
		$this->assertNull($this->invoke($this->indexer, 'convert', $this->getJob('add', ['document.data' => 'data', 'document.class' => 'class', 'document.format' => 'format'])));
	}

	public function testConvertAddNoDeserializeWithId()
	{
		$this->indexer = $this->getIndexer(null, true);

		$command = $this->invoke($this->indexer, 'convert', $this->getJob('add', ['document.id' => 'test-id']));

		$this->assertInstanceOf('Solarium\QueryType\Update\Query\Command\Delete', $command);
		$this->assertEquals(['test-id'], $command->getIds());
	}

	/**
	 * @expectedException \Integrated\Common\Solr\Exception\SerializerException
	 */
	public function testConvertAddDeserializeError()
	{
		$this->serializer->expects($this->once())->method('deserialize')->will($this->throwException(new \Exception()));

		$this->indexer = $this->getIndexer(null, true);

		$this->invoke($this->indexer, 'convert', $this->getJob('add', ['document.data' => 'data', 'document.class' => 'class', 'document.format' => 'format']));
	}

	public function testConvertAddInvalidOption()
	{
		$this->indexer = $this->getIndexer();
		$this->assertNull($this->invoke($this->indexer, 'convert', $this->getJob('add', ['me-is-invalid' => 'really'])));
	}

	public function testConvertDeleteId()
	{
		$this->indexer = $this->getIndexer();

		$command = $this->invoke($this->indexer, 'convert', $this->getJob('delete', array('id' => 'test-id')));

		$this->assertInstanceOf('Solarium\QueryType\Update\Query\Command\Delete', $command);
		$this->assertEmpty($command->getOptions());
		$this->assertEquals(['test-id'], $command->getIds());
	}

	public function testConvertDeleteQuery()
	{
		$this->indexer = $this->getIndexer();

		$command = $this->invoke($this->indexer, 'convert', $this->getJob('delete', array('query' => 'test-query')));

		$this->assertInstanceOf('Solarium\QueryType\Update\Query\Command\Delete', $command);
		$this->assertEmpty($command->getOptions());
		$this->assertEquals(['test-query'], $command->getQueries());
	}

	public function testConvertDeleteBoth()
	{
		$this->indexer = $this->getIndexer();

		$command = $this->invoke($this->indexer, 'convert', $this->getJob('delete', array('id' => 'test-id', 'query' => 'test-query')));

		$this->assertInstanceOf('Solarium\QueryType\Update\Query\Command\Delete', $command);
		$this->assertEquals(['test-id'], $command->getIds());
		$this->assertEquals(['test-query'], $command->getQueries());
	}

	public function testConvertDeleteMissingOption()
	{
		$this->indexer = $this->getIndexer();
		$this->assertNull($this->invoke($this->indexer, 'convert', $this->getJob('delete')));
	}

	public function testConvertDeleteInvalidOption()
	{
		$this->indexer = $this->getIndexer();
		$this->assertNull($this->invoke($this->indexer, 'convert', $this->getJob('delete', ['me-is-invalid' => 'really'])));
	}

	public function testConvertOptimize()
	{
		$this->indexer = $this->getIndexer();

		$command = $this->invoke($this->indexer, 'convert', $this->getJob('optimize'));

		$this->assertInstanceOf('Solarium\QueryType\Update\Query\Command\Optimize', $command);
		$this->assertEmpty($command->getOptions());
	}

	public function testConvertOptimizeWithOption()
	{
		$this->indexer = $this->getIndexer();

		$command = $this->invoke($this->indexer, 'convert', $this->getJob('optimize', ['maxsegments' => true, 'waitsearcher' => false, 'softcommit' => true]));

		$this->assertInstanceOf('Solarium\QueryType\Update\Query\Command\Optimize', $command);
		$this->assertEquals(['maxsegments' => true, 'waitsearcher' => false, 'softcommit' => true], $command->getOptions());
	}

	public function testConvertOptimizeWithOptionNoneBool()
	{
		$this->indexer = $this->getIndexer();

		$command = $this->invoke($this->indexer, 'convert', $this->getJob('optimize', ['maxsegments' => 'yes', 'waitsearcher' => null, 'softcommit' => 'no']));

		$this->assertInstanceOf('Solarium\QueryType\Update\Query\Command\Optimize', $command);
		$this->assertEquals(['maxsegments' => true, 'waitsearcher' => false, 'softcommit' => true], $command->getOptions());
	}

	public function testConvertOptimizeInvalidOption()
	{
		$this->indexer = $this->getIndexer();

		$command = $this->invoke($this->indexer, 'convert', $this->getJob('optimize', ['me-is-invalid' => 'really']));

		$this->assertInstanceOf('Solarium\QueryType\Update\Query\Command\Optimize', $command);
		$this->assertEmpty($command->getOptions());
	}

	public function testConvertCommit()
	{
		$this->indexer = $this->getIndexer();

		$command = $this->invoke($this->indexer, 'convert', $this->getJob('commit'));

		$this->assertInstanceOf('Solarium\QueryType\Update\Query\Command\Commit', $command);
		$this->assertEmpty($command->getOptions());
	}

	public function testConvertCommitWithOption()
	{
		$this->indexer = $this->getIndexer();

		$command = $this->invoke($this->indexer, 'convert', $this->getJob('commit', ['expungedeletes' => true, 'waitsearcher' => false, 'softcommit' => true]));

		$this->assertInstanceOf('Solarium\QueryType\Update\Query\Command\Commit', $command);
		$this->assertEquals(['expungedeletes' => true, 'waitsearcher' => false, 'softcommit' => true], $command->getOptions());
	}

	public function testConvertCommitWithOptionNoneBool()
	{
		$this->indexer = $this->getIndexer();

		$command = $this->invoke($this->indexer, 'convert', $this->getJob('commit', ['expungedeletes' => 'yes', 'waitsearcher' => null, 'softcommit' => 'no']));

		$this->assertInstanceOf('Solarium\QueryType\Update\Query\Command\Commit', $command);
		$this->assertEquals(['expungedeletes' => true, 'waitsearcher' => false, 'softcommit' => true], $command->getOptions());
	}

	public function testConvertCommitInvalidOption()
	{
		$this->indexer = $this->getIndexer();

		$command = $this->invoke($this->indexer, 'convert', $this->getJob('commit', ['me-is-invalid' => 'really']));

		$this->assertInstanceOf('Solarium\QueryType\Update\Query\Command\Commit', $command);
		$this->assertEmpty($command->getOptions());
	}

	public function testConvertRollback()
	{
		$this->indexer = $this->getIndexer();

		$command = $this->invoke($this->indexer, 'convert', $this->getJob('rollback'));

		$this->assertInstanceOf('Solarium\QueryType\Update\Query\Command\Rollback', $command);
		$this->assertEmpty($command->getOptions());
	}

	public function testConvertRollbackInvalidOption()
	{
		$this->indexer = $this->getIndexer();

		$command = $this->invoke($this->indexer, 'convert', $this->getJob('rollback', ['me-is-invalid' => 'really']));

		$this->assertInstanceOf('Solarium\QueryType\Update\Query\Command\Rollback', $command);
		$this->assertEmpty($command->getOptions());
	}

	public function testSend()
	{
		// test that after the command are converted to a solarium query that
		// the messages are deleted and that the batch is cleared.

		$message1 = $this->getMock('Integrated\Common\Queue\QueueMessageInterface');
		$message2 = $this->getMock('Integrated\Common\Queue\QueueMessageInterface');

		$operation1 = $this->getMockBuilder('Integrated\Common\Solr\Indexer\BatchOperation')->disableOriginalConstructor()->getMock();
		$operation1->expects($this->atLeastOnce())->method('getCommand')->will($this->returnValue($this->getMock('Solarium\QueryType\Update\Query\Command\Command')));
		$operation1->expects($this->atLeastOnce())->method('getMessage')->will($this->returnValue($message1));

		$operation2 = $this->getMockBuilder('Integrated\Common\Solr\Indexer\BatchOperation')->disableOriginalConstructor()->getMock();
		$operation2->expects($this->atLeastOnce())->method('getCommand')->will($this->returnValue($this->getMock('Solarium\QueryType\Update\Query\Command\Command')));
		$operation2->expects($this->atLeastOnce())->method('getMessage')->will($this->returnValue($message2));

		$this->dispatcher->expects($this->exactly(2))->method('dispatch');

		$this->client->expects($this->atLeastOnce())->method('createUpdate')->will($this->returnValue($this->getMock('Solarium\QueryType\Update\Query\Query')));
		$this->client->expects($this->once())->method('execute')->will($this->returnValue($this->getMock('Solarium\Core\Query\Result\ResultInterface')));

		$this->indexer = $this->getIndexer(['delete'], true);

		$this->indexer->expects($this->at(0))->method('delete')->with($this->identicalTo($message1));
		$this->indexer->expects($this->at(1))->method('delete')->with($this->identicalTo($message2));

		/** @var Batch $batch */

		$batch = $this->invoke($this->indexer, 'getBatch');

		$batch->add($operation1);
		$batch->add($operation2);

		$this->invoke($this->indexer, 'send');

		$this->assertCount(0, $batch);
	}

	/**
	 * @expectedException \Integrated\Common\Solr\Exception\ClientException
	 */
	public function testSendWithSolariumError()
	{
		// test if a error in the solarium client is converted to a ClientException

		$operation = $this->getMockBuilder('Integrated\Common\Solr\Indexer\BatchOperation')->disableOriginalConstructor()->getMock();
		$operation->expects($this->any())->method('getCommand')->will($this->returnValue($this->getMock('Solarium\QueryType\Update\Query\Command\Command')));
		$operation->expects($this->any())->method('getMessage')->will($this->returnValue($this->getMock('Integrated\Common\Queue\QueueMessageInterface')));

		$this->dispatcher->expects($this->exactly(1))->method('dispatch')->with($this->identicalTo(Events::SENDING));

		$this->client->expects($this->atLeastOnce())->method('createUpdate')->will($this->returnValue($this->getMock('Solarium\QueryType\Update\Query\Query')));
		$this->client->expects($this->once())->method('execute')->will($this->throwException(new \Exception()));

		$this->indexer = $this->getIndexer(['delete'], true);
		$this->indexer->expects($this->never())->method('delete');

		/** @var Batch $batch */

		$batch = $this->invoke($this->indexer, 'getBatch');
		$batch->add($operation);

		$this->invoke($this->indexer, 'send');
	}

	public function testSendEmptyBatch()
	{
		$this->dispatcher->expects($this->never())->method('dispatch');
		$this->client->expects($this->never())->method('execute');

		$this->indexer = $this->getIndexer(null, true);
		$this->invoke($this->indexer, 'send');
	}

	public function testSendEventDispatch()
	{
		// test if the event dispatcher is called in the right order and with
		// the correct arguments.

		$operation = $this->getMockBuilder('Integrated\Common\Solr\Indexer\BatchOperation')->disableOriginalConstructor()->getMock();
		$operation->expects($this->any())->method('getCommand')->will($this->returnValue($this->getMock('Solarium\QueryType\Update\Query\Command\Command')));
		$operation->expects($this->any())->method('getMessage')->will($this->returnValue($this->getMock('Integrated\Common\Queue\QueueMessageInterface')));

		$query = $this->getMock('Solarium\QueryType\Update\Query\Query');
		$result = $this->getMock('Solarium\Core\Query\Result\ResultInterface');

		$callback = function($value) use ($query) {
			return $value instanceof \Integrated\Common\Solr\Indexer\Event\SendEvent && $value->getIndexer() === $this->indexer && $value->getQuery() === $query;
		};

		$this->dispatcher->expects($this->at(0))->method('dispatch')->with($this->identicalTo(Events::SENDING), $this->callback($callback));

		$callback = function($value) use ($result) {
			return $value instanceof \Integrated\Common\Solr\Indexer\Event\ResultEvent && $value->getIndexer() === $this->indexer && $value->getResult() === $result;
		};

		$this->dispatcher->expects($this->at(1))->method('dispatch')->with($this->identicalTo(Events::RESULTS), $this->callback($callback));

		$this->client->expects($this->atLeastOnce())->method('createUpdate')->will($this->returnValue($query));
		$this->client->expects($this->once())->method('execute')->will($this->returnValue($result));

		$this->indexer = $this->getIndexer(['delete'], true);

		/** @var Batch $batch */

		$batch = $this->invoke($this->indexer, 'getBatch');
		$batch->add($operation);

		$this->invoke($this->indexer, 'send');
	}

	public function testDelete()
	{
		$this->indexer = $this->getIndexer([], true);

		$message = $this->getMock('Integrated\Common\Queue\QueueMessageInterface');
		$message->expects($this->atLeastOnce())->method('delete');

		$callback = function($value) use ($message) {
			return $value instanceof \Integrated\Common\Solr\Indexer\Event\MessageEvent && $value->getIndexer() === $this->indexer && $value->getMessage() === $message;
		};

		$this->dispatcher->expects($this->exactly(1))->method('dispatch')->with($this->identicalTo(Events::PROCESSED), $this->callback($callback));

		$this->invoke($this->indexer, 'delete', $message);
	}

	public function testClean()
	{
		$this->indexer = $this->getIndexer();

		/** @var Batch $batch */

		$batch = $this->invoke($this->indexer, 'getBatch');
		$batch->add($this->getMockBuilder('Integrated\Common\Solr\Indexer\BatchOperation')->disableOriginalConstructor()->getMock());

		$this->invoke($this->indexer, 'clean');

		$this->assertEquals(0, $batch->count());
	}

	/**
	 * Call a protected method on the given object.
	 *
	 * The invoke method takes a unlimited number of optional arguments
	 * that will be pasted to the protected method of the object.
	 *
	 * @param Object $obj
	 * @param String $method
	 * @return mixed
	 */
	protected function invoke($obj, $method)
	{
		$class = new \ReflectionClass($obj);

		$method = $class->getMethod($method);
		$method->setAccessible(true);

		if (func_num_args() > 2) {
			$args = func_get_args();

			array_shift($args);
			array_shift($args);

			return $method->invokeArgs($obj, $args);
		}

		return $method->invoke($obj);
	}

	/**
	 * Create a job moch with the supplied options.
	 *
	 * @param string $action
	 * @param array $options
	 * @return JobInterface | \PHPUnit_Framework_MockObject_MockObject
	 */
	protected function getJob($action = null, array $options = [])
	{
		$job = $this->getMock('Integrated\Common\Solr\Indexer\JobInterface');

		if ($action) {
			$job->expects($this->atLeastOnce())
				->method('hasAction')
				->will($this->returnValue(true));

			$job->expects($this->atLeastOnce())
				->method('getAction')
				->will($this->returnValue($action));
		} else {
			$job->expects($this->atLeastOnce())
				->method('hasAction')
				->will($this->returnValue(false));

			$job->expects($this->any())
				->method('getAction')
				->will($this->returnValue(null));
		}

		if ($options) {
			$map = [];

			foreach ($options as $key => $value) {
				$map[] = [$key, true];
			}

			$job->expects($this->any())
				->method('hasOption')
				->will($this->returnValueMap($map)); // bit of a hax as none existing options will give null while they should give false

			$map = [];

			foreach ($options as $key => $value) {
				$map[] = [$key, $value];
			}

			$job->expects($this->any())
				->method('getOption')
				->will($this->returnValueMap($map));
		} else {
			$job->expects($this->any())
				->method('hasOption')
				->will($this->returnValue(false));

			$job->expects($this->any())
				->method('getOption')
				->will($this->returnValue(null));
		}

		return $job;
	}

	/**
	 * Create a indexer mock so that individual method can be test with out
	 * having to worry about side effects of other methods.
	 *
	 * The method given in the methods array will be mocked everything else
	 * will be untouched. So supplying a empty array wil essentially be getting
	 * a plain indexer object with out changes.
	 *
	 * If add addDependencies is set to true then mocks of the EventDispatcher,
	 * Queue, Serializer, Converter and Solarium Client will be inserted.
	 *
	 * @param array $methods
	 * @param bool $addDependencies
	 * @return Indexer | \PHPUnit_Framework_MockObject_MockObject
	 */
	protected function getIndexer($methods = [], $addDependencies = false)
	{
		if (empty($methods)) {
			$methods = null;
		}

		/** @var Indexer $indexer */
		$indexer = $this->getMock('Integrated\Common\Solr\Indexer\Indexer', $methods);

		if ($addDependencies) {
			$indexer->setEventDispatcher($this->dispatcher);
			$indexer->setQueue($this->queue);
			$indexer->setSerializer($this->serializer);
			$indexer->setConverter($this->converter);
			$indexer->setClient($this->client);
		}

		return $indexer;
	}
}