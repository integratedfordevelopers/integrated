<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WorkflowBundle\Tests\Doctrine\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Integrated\Bundle\WorkflowBundle\Doctrine\EventListener\QueueListener;
use Integrated\Bundle\WorkflowBundle\Entity\Definition;
use Integrated\Bundle\WorkflowBundle\Entity\Definition\State;
use Integrated\Common\Queue\QueueInterface;
use stdClass;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class QueueListenerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var QueueInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $queue;

    protected function setUp(): void
    {
        $this->queue = $this->createMock('Integrated\\Common\\Queue\\QueueInterface');
    }

    public function testInterface()
    {
        $this->assertInstanceOf('Doctrine\\Common\\EventSubscriber', $this->getInstance());
    }

    public function testGetSubscribedEvents()
    {
        $this->assertEquals([
            Events::postPersist,
            Events::postUpdate,
        ], $this->getInstance()->getSubscribedEvents());
    }

    public function testSetGetQueue()
    {
        $listener = $this->getInstance();
        $mock = $this->createMock('Integrated\\Common\\Queue\\QueueInterface');

        $this->assertSame($this->queue, $listener->getQueue());
        $listener->setQueue($mock);
        $this->assertSame($mock, $listener->getQueue());
    }

    public function testPostPersist()
    {
        $event = $this->getEvent($this->getDefinition('this-is-the-id'));

        $this->queue->expects($this->once())
            ->method('push')
            ->with($this->equalTo(['command' => 'index', 'args' => ['this-is-the-id']]));

        $instance = $this->getInstance();

        $instance->postPersist($event);
        $instance->postPersist($event);
    }

    public function testPostPersistNoWorkflow()
    {
        $event = $this->getEvent(new stdClass());

        $this->queue->expects($this->never())
            ->method('push');

        $this->getInstance()->postPersist($event);
    }

    public function testPostPersistState()
    {
        $event = $this->getEvent($this->getState('this-is-the-id'));

        $this->queue->expects($this->once())
            ->method('push')
            ->with($this->equalTo(['command' => 'index', 'args' => ['this-is-the-id']]));

        $instance = $this->getInstance();

        $instance->postPersist($event);
        $instance->postPersist($event);
    }

    public function testPostPersistNoDoublePush()
    {
        $this->queue->expects($this->once())
            ->method('push')
            ->with($this->equalTo(['command' => 'index', 'args' => ['this-is-the-id']]));

        $instance = $this->getInstance();

        $instance->postPersist($this->getEvent($this->getState('this-is-the-id')));
        $instance->postPersist($this->getEvent($this->getDefinition('this-is-the-id')));
    }

    public function testPostUpdate()
    {
        $event = $this->getEvent($this->getDefinition('this-is-the-id'));

        $this->queue->expects($this->once())
            ->method('push')
            ->with($this->equalTo(['command' => 'index', 'args' => ['this-is-the-id']]));

        $instance = $this->getInstance();

        $instance->postUpdate($event);
        $instance->postUpdate($event);
    }

    public function testPostUpdateNoWorkflow()
    {
        $event = $this->getEvent(new stdClass());

        $this->queue->expects($this->never())
            ->method('push');

        $this->getInstance()->postUpdate($event);
    }

    public function testPostUpdateState()
    {
        $event = $this->getEvent($this->getState('this-is-the-id'));

        $this->queue->expects($this->once())
            ->method('push')
            ->with($this->equalTo(['command' => 'index', 'args' => ['this-is-the-id']]));

        $instance = $this->getInstance();

        $instance->postUpdate($event);
        $instance->postUpdate($event);
    }

    public function testPostUpdateNoDoublePush()
    {
        $this->queue->expects($this->once())
            ->method('push')
            ->with($this->equalTo(['command' => 'index', 'args' => ['this-is-the-id']]));

        $instance = $this->getInstance();

        $instance->postUpdate($this->getEvent($this->getState('this-is-the-id')));
        $instance->postUpdate($this->getEvent($this->getDefinition('this-is-the-id')));
    }

    /**
     * @param $object
     *
     * @return LifecycleEventArgs | \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getEvent($object)
    {
        $instance = $this->getMockBuilder('Doctrine\\ORM\\Event\\LifecycleEventArgs')->disableOriginalConstructor()->getMock();
        $instance->expects($this->any())
            ->method('getObject')
            ->willReturn($object);

        return $instance;
    }

    /**
     * @param $id
     *
     * @return State | \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getState($id)
    {
        $instance = $this->createMock('Integrated\\Bundle\\WorkflowBundle\\Entity\\Definition\\State');
        $instance->expects($this->any())
            ->method('getWorkflow')
            ->willReturn($this->getDefinition($id));

        return $instance;
    }

    /**
     * @param $id
     *
     * @return Definition | \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getDefinition($id)
    {
        $instance = $this->createMock('Integrated\\Bundle\\WorkflowBundle\\Entity\\Definition');
        $instance->expects($this->any())
            ->method('getId')
            ->willReturn($id);

        return $instance;
    }

    /**
     * @return QueueListener
     */
    protected function getInstance()
    {
        return new QueueListener($this->queue);
    }
}
