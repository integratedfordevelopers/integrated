<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\MongoDB\Solr\Tests\Indexer;

use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Integrated\Common\Content\ContentInterface;
use Integrated\Common\Queue\QueueInterface;
use Integrated\MongoDB\Solr\Indexer\QueueSubscriber;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class QueueSubscriberTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var QueueInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $queue;

    /**
     * @var SerializerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $serializer;

    /**
     * @var QueueSubscriber
     */
    private $subscriber;

    protected function setUp(): void
    {
        $this->queue = $this->createMock('Integrated\\Common\\Queue\\QueueInterface');
        $this->serializer = $this->createMock('Symfony\\Component\\Serializer\\SerializerInterface');

        $this->subscriber = new QueueSubscriber($this->queue, $this->serializer);
    }

    public function testInterface()
    {
        $this->assertInstanceOf('Doctrine\\Common\\EventSubscriber', $this->subscriber);
        $this->assertInstanceOf('Integrated\\Common\\Queue\\QueueAwareInterface', $this->subscriber);
        $this->assertInstanceOf('Symfony\\Component\\Serializer\\SerializerAwareInterface', $this->subscriber);
    }

    public function testSetAndGetQueue()
    {
        $this->assertSame($this->queue, $this->subscriber->getQueue());

        $mock = $this->createMock('Integrated\\Common\\Queue\\QueueInterface');
        $this->subscriber->setQueue($mock);

        $this->assertSame($mock, $this->subscriber->getQueue());
    }

    public function testSetAndGetSerializer()
    {
        $this->assertSame($this->serializer, $this->subscriber->getSerializer());

        $mock = $this->createMock('Symfony\\Component\\Serializer\\SerializerInterface');
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

    public function testSetAndGetPriority()
    {
        $this->assertSame(0, $this->subscriber->getPriority());

        $this->subscriber->setPriority(42);

        $this->assertSame(42, $this->subscriber->getPriority());
    }

    public function testGetSubscribedEvents()
    {
        $this->assertEquals(
            [Events::postPersist, Events::postUpdate, Events::postRemove],
            $this->subscriber->getSubscribedEvents()
        );
    }

    public function testPostPersist()
    {
        $document = $this->getDocument('this-is-the-id', 'this-is-the-type');
        $manager = $this->getManager($document);
        $event = $this->getEvent($document, $manager);

        $this->serializer->expects($this->atLeastOnce())
            ->method('serialize')
            ->with($this->identicalTo($document), $this->identicalTo('json'))
            ->willReturn('this-is-the-data');

        $callback = function ($value) use ($document) {
            return $value instanceof \Integrated\Common\Solr\Indexer\JobInterface
                && strtolower($value->getAction()) === 'add'
                && $value->getOption('document.id') === 'this-is-the-type-this-is-the-id'
                && $value->getOption('document.data') === 'this-is-the-data'
                && $value->getOption('document.class') === \get_class($document)
                && $value->getOption('document.format') === 'json';
        };

        $this->queue->expects($this->once())
            ->method('push')
            ->with($this->callback($callback), $this->identicalTo(0), $this->identicalTo(0));

        $this->subscriber->postPersist($event);
    }

    public function testPostUpdate()
    {
        $document = $this->getDocument('this-is-the-id', 'this-is-the-type');
        $manager = $this->getManager($document);
        $event = $this->getEvent($document, $manager);

        $this->serializer->expects($this->atLeastOnce())
            ->method('serialize')
            ->with($this->identicalTo($document), $this->identicalTo('json'))
            ->willReturn('this-is-the-data');

        $callback = function ($value) use ($document) {
            return $value instanceof \Integrated\Common\Solr\Indexer\JobInterface
                && strtolower($value->getAction()) === 'add'
                && $value->getOption('document.id') === 'this-is-the-type-this-is-the-id'
                && $value->getOption('document.data') === 'this-is-the-data'
                && $value->getOption('document.class') === \get_class($document)
                && $value->getOption('document.format') === 'json';
        };

        $this->queue->expects($this->once())
            ->method('push')
            ->with($this->callback($callback), $this->identicalTo(0), $this->identicalTo(0));

        $this->subscriber->postUpdate($event);
    }

    public function testPostRemove()
    {
        $event = $this->getEvent($this->getDocument('this-is-the-id', 'this-is-the-type'));

        $callback = function ($value) {
            return $value instanceof \Integrated\Common\Solr\Indexer\JobInterface
                && strtolower($value->getAction()) === 'delete'
                && $value->getOption('id') === 'this-is-the-type-this-is-the-id';
        };

        $this->queue->expects($this->once())
            ->method('push')
            ->with($this->callback($callback), $this->identicalTo(0), $this->identicalTo(0));

        $this->subscriber->postRemove($event);
    }

    public function testPriority()
    {
        $event = $this->getEvent($this->getDocument('this-is-the-id', 'this-is-the-type'));

        $this->queue->expects($this->exactly(2))
            ->method('push')
            ->with($this->anything(), $this->identicalTo(0), $this->identicalTo(42));

        $this->subscriber->setPriority(42);
        $this->subscriber->postRemove($event); // remove requires the least setup
    }

    /**
     * @param string $id
     * @param string $type
     *
     * @return ContentInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getDocument($id, $type)
    {
        $mock = $this->createMock('Integrated\\Common\\Content\\ContentInterface');
        $mock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($id);

        $mock->expects($this->atLeastOnce())
            ->method('getContentType')
            ->willReturn($type);

        return $mock;
    }

    protected function getManager($content)
    {
        $mockMeta = $this->createMock('Doctrine\\ODM\\MongoDB\\Mapping\\ClassMetadata');
        $mockMeta->expects($this->once())
            ->method('getName')
            ->willReturn(\get_class($content));

        $mock = $this->createMock('Doctrine\\ODM\MongoDB\\DocumentManager');
        $mock->expects($this->once())
            ->method('getClassMetadata')
            ->willReturn($mockMeta);

        return $mock;
    }

    /**
     * @param object $document
     *
     * @return LifecycleEventArgs|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getEvent($document, $manager = null)
    {
        $mock = $this->getMockBuilder('Doctrine\\ODM\MongoDB\\Event\\LifecycleEventArgs')
            ->disableOriginalConstructor()
            ->getMock();

        $mock->expects($this->atLeastOnce())
            ->method('getDocument')
            ->willReturn($document);

        if ($manager) {
            $mock->expects($this->once())
                ->method('getDocumentManager')
                ->willReturn($manager);
        }

        return $mock;
    }
}
