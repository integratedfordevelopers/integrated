<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr\Tests\Task\Tasks\Doctrine\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\Queue\QueueInterface;
use Integrated\Common\Solr\Task\Tasks\ContentTypeQueueTask;
use Integrated\Common\Solr\Task\Tasks\Doctrine\EventListener\MongoDBContentTypeListener;
use stdClass;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class MongoDBContenTypeListenerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var QueueInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $queue;

    protected function setUp(): void
    {
        $this->queue = $this->createMock(QueueInterface::class);
    }

    public function testInterface()
    {
        $this->assertInstanceOf(EventSubscriber::class, $this->getInstance());
    }

    public function testGetSubscribedEvents()
    {
        self::assertEquals([
            Events::postUpdate,
        ], $this->getInstance()->getSubscribedEvents());
    }

    public function testPostUpdate()
    {
        $this->queue->expects($this->once())
            ->method('push')
            ->with($this->callback(function (ContentTypeQueueTask $task) {
                self::assertEquals('this-is-the-id', $task->getId());

                return true;
            }));

        $this->getInstance()->postUpdate($this->getEvent($this->getContentType('this-is-the-id')));
    }

    public function testPostUpdateNoContentType()
    {
        $this->queue->expects($this->never())
            ->method($this->anything());

        $this->getInstance()->postUpdate($this->getEvent(new stdClass()));
    }

    /**
     * @return MongoDBContentTypeListener
     */
    protected function getInstance()
    {
        return new MongoDBContentTypeListener($this->queue);
    }

    /**
     * @param string $id
     *
     * @return ContentTypeInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getContentType($id)
    {
        $mock = $this->createMock(ContentTypeInterface::class);
        $mock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($id);

        return $mock;
    }

    /**
     * @param object $document
     *
     * @return LifecycleEventArgs | \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getEvent($document)
    {
        $mock = $this->getMockBuilder(LifecycleEventArgs::class)->disableOriginalConstructor()->getMock();
        $mock->expects($this->atLeastOnce())
            ->method('getDocument')
            ->willReturn($document);

        return $mock;
    }
}
