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
		$this->assertInstanceOf('Doctrine\Common\EventSubscriber', $this->queue);
		$this->assertInstanceOf('Integrated\Common\Queue\QueueAwareInterface', $this->queue);
		$this->assertInstanceOf('Symfony\Component\Serializer\SerializerAwareInterface', $this->queue);
		$this->assertInstanceOf('Integrated\Common\Solr\Converter\ConverterAwareInterface', $this->queue);
	}

	public function testGetQueue()
	{
		$this->assertSame($this->queue, $this->subscriber->getQueue());

		$mock = $this->getMock('Integrated\Common\Queue\QueueInterface');
		$this->subscriber->setQueue($mock);

		$this->assertSame($mock, $this->subscriber->getQueue());
	}

	public function testGetSerializer()
	{
		$this->assertSame($this->serializer, $this->subscriber->getSerializer());

		$mock = $this->getMock('Symfony\Component\Serializer\SerializerAwareInterface');
		$this->subscriber->setSerializer($mock);

		$this->assertSame($mock, $this->subscriber->getSerializer());
	}

	public function testGetSerializerFormat()
	{
		$this->subscriber->setSerializer('format');
		$this->assertEquals('format', $this->subscriber->getSerializerFormat());
	}

	public function testGetSerializerFormatDefault()
	{
		$this->assertEquals('json', $this->subscriber->getSerializerFormat());
	}

	public function testGetConverter()
	{
		$this->assertSame($this->serializer, $this->subscriber->getConverter());

		$mock = $this->getMock('Integrated\Common\Solr\Converter\ConverterAwareInterface');
		$this->subscriber->setConverter($mock);

		$this->assertSame($mock, $this->subscriber->getConverter());
	}

	public function testPostPersist()
	{
		$this->markTestIncomplete();
	}

	public function testPostUpdate()
	{
		$this->markTestIncomplete();
	}

	public function testPostRemove()
	{
		$this->markTestIncomplete();
	}
}