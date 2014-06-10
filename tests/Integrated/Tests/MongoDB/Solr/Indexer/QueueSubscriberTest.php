<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\MongoDB\Solr\Indexer;

use Doctrine\ODM\MongoDB\Events;

use Integrated\Common\Queue\QueueInterface;
use Integrated\Common\Solr\Converter\ConverterInterface;
use Integrated\MongoDB\Solr\Indexer\QueueSubscriber;

use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class QueueSubscriberTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var QueueInterface | \PHPUnit_Framework_MockObject_MockObject
	 */
	private $queue;

	/**
	 * @var SerializerInterface | \PHPUnit_Framework_MockObject_MockObject
	 */
	private $serializer;

	/**
	 * @var ConverterInterface | \PHPUnit_Framework_MockObject_MockObject
	 */
	private $converter;

	/**
	 * @var QueueSubscriber
	 */
	private $subscriber;

	protected function setUp()
	{
		$this->queue = $this->getMock('Integrated\Common\Queue\QueueInterface');
		$this->serializer = $this->getMock('Symfony\Component\Serializer\SerializerInterface');
		$this->converter = $this->getMock('Integrated\Common\Solr\Converter\ConverterInterface');

		$this->subscriber = new QueueSubscriber($this->queue, $this->serializer, $this->converter);
	}

	public function testInterface()
	{
		$this->assertInstanceOf('Doctrine\Common\EventSubscriber', $this->subscriber);
		$this->assertInstanceOf('Integrated\Common\Queue\QueueAwareInterface', $this->subscriber);
		$this->assertInstanceOf('Symfony\Component\Serializer\SerializerAwareInterface', $this->subscriber);
		$this->assertInstanceOf('Integrated\Common\Solr\Converter\ConverterAwareInterface', $this->subscriber);
	}

	public function testSetAndGetQueue()
	{
		$this->assertSame($this->queue, $this->subscriber->getQueue());

		$mock = $this->getMock('Integrated\Common\Queue\QueueInterface');
		$this->subscriber->setQueue($mock);

		$this->assertSame($mock, $this->subscriber->getQueue());
	}

	public function testSetAndGetSerializer()
	{
		$this->assertSame($this->serializer, $this->subscriber->getSerializer());

		$mock = $this->getMock('Symfony\Component\Serializer\SerializerInterface');
		$this->subscriber->setSerializer($mock);

		$this->assertSame($mock, $this->subscriber->getSerializer());
	}

	public function testSetAndGetSerializerFormat()
	{
		$this->subscriber->setSerializerFormat('format');
		$this->assertEquals('format', $this->subscriber->getSerializerFormat());
	}

	public function testGetDefaultSerializerFormat()
	{
		$this->assertEquals('json', $this->subscriber->getSerializerFormat());
	}

	public function testSetAndGetConverter()
	{
		$this->assertSame($this->converter, $this->subscriber->getConverter());

		$mock = $this->getMock('Integrated\Common\Solr\Converter\ConverterInterface');
		$this->subscriber->setConverter($mock);

		$this->assertSame($mock, $this->subscriber->getConverter());
	}

	public function testGetSubscribedEvents()
	{
		$this->assertEquals([Events::postPersist, Events::postUpdate, Events::postRemove], $this->subscriber->getSubscribedEvents());
	}

	public function testPostPersist()
	{
		$document = $this->getMock('Integrated\Common\Content\ContentInterface');

		$event = $this->getMockBuilder('Doctrine\ODM\MongoDB\Event\LifecycleEventArgs')->disableOriginalConstructor()->getMock();
		$event->expects($this->atLeastOnce())->method('getDocument')->will($this->returnValue($document));

		$this->converter->expects($this->atLeastOnce())->method('getId')->will($this->returnValue('this-is-the-id'));
		$this->serializer->expects($this->atLeastOnce())->method('serialize')->with($this->identicalTo($document), $this->identicalTo('json'))->will($this->returnValue('this-is-the-data'));

		$callback = function($value) use ($document) {
			return $value instanceof \Integrated\Common\Solr\Indexer\JobInterface && strtolower($value->getAction()) === 'add' && $value->getOption('document.id') === 'this-is-the-id'
				&& $value->getOption('document.data') === 'this-is-the-data' && $value->getOption('document.class') === get_class($document) && $value->getOption('document.format') === 'json';
		};

		$this->queue->expects($this->once())->method('push')->with($this->callback($callback));

		$this->subscriber->postPersist($event);
	}

	public function testPostUpdate()
	{
		$document = $this->getMock('Integrated\Common\Content\ContentInterface');

		$event = $this->getMockBuilder('Doctrine\ODM\MongoDB\Event\LifecycleEventArgs')->disableOriginalConstructor()->getMock();
		$event->expects($this->atLeastOnce())->method('getDocument')->will($this->returnValue($document));

		$this->converter->expects($this->atLeastOnce())->method('getId')->will($this->returnValue('this-is-the-id'));
		$this->serializer->expects($this->atLeastOnce())->method('serialize')->with($this->identicalTo($document), $this->identicalTo('json'))->will($this->returnValue('this-is-the-data'));

		$callback = function($value) use ($document) {
			return $value instanceof \Integrated\Common\Solr\Indexer\JobInterface && strtolower($value->getAction()) === 'add' && $value->getOption('document.id') === 'this-is-the-id'
				&& $value->getOption('document.data') === 'this-is-the-data' && $value->getOption('document.class') === get_class($document) && $value->getOption('document.format') === 'json';
		};

		$this->queue->expects($this->once())->method('push')->with($this->callback($callback));

		$this->subscriber->postUpdate($event);
	}

	public function testPostRemove()
	{
		$document = $this->getMock('Integrated\Common\Content\ContentInterface');

		$event = $this->getMockBuilder('Doctrine\ODM\MongoDB\Event\LifecycleEventArgs')->disableOriginalConstructor()->getMock();
		$event->expects($this->atLeastOnce())->method('getDocument')->will($this->returnValue($document));

		$this->converter->expects($this->atLeastOnce())->method('getId')->will($this->returnValue('this-is-the-id'));

		$callback = function($value) {
			return $value instanceof \Integrated\Common\Solr\Indexer\JobInterface && strtolower($value->getAction()) === 'delete' && $value->getOption('id') === 'this-is-the-id';
		};

		$this->queue->expects($this->once())->method('push')->with($this->callback($callback));

		$this->subscriber->postRemove($event);
	}
}