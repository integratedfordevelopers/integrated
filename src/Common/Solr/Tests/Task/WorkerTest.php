<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr\Tests\Task;

use Exception;
use Integrated\Common\Queue\QueueInterface;
use Integrated\Common\Queue\QueueMessageInterface;
use Integrated\Common\Solr\Task\Event\ErrorEvent;
use Integrated\Common\Solr\Task\Event\WorkerEvent;
use Integrated\Common\Solr\Task\Registry;
use Integrated\Common\Solr\Task\Worker;
use stdClass;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class WorkerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    private $registry;

    /**
     * @var QueueInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $queue;

    /**
     * @var EventDispatcherInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dispatcher;

    protected function setUp(): void
    {
        $this->registry = $this->getMockBuilder(Registry::class)->disableOriginalConstructor()->getMock();
        $this->queue = $this->createMock(QueueInterface::class);
        $this->dispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->disableArgumentCloning()->getMock();
    }

    public function testOptions()
    {
        $instance = $this->getInstance();

        self::assertSame(['tasks' => 1000], $instance->getOptions());

        $instance->setOption('tasks', 5000);

        self::assertSame(['tasks' => 5000], $instance->getOptions());
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

        $task1 = new stdClass();
        $task2 = new stdClass();

        $this->queue->expects($this->atLeastOnce())
            ->method('pull')
            ->willReturnOnConsecutiveCalls([$this->getMessage($task1)], [$this->getMessage($task2)], []);

        $callback = [
            function ($argument) use ($task1) {
                self::assertSame($task1, $argument);
            },
            function ($argument) use ($task2) {
                self::assertSame($task2, $argument);
            },
        ];

        $this->registry->expects($this->exactly(2))
            ->method('getHandler')
            ->with($this->equalTo('stdClass'))
            ->willReturnOnConsecutiveCalls($callback[0], $callback[1]);

        $callback = function (WorkerEvent $event) use ($instance) {
            self::assertSame($instance, $event->getWorker());

            return true;
        };

        $this->dispatcher->expects($this->exactly(2))
            ->method('dispatch')
            ->withConsecutive(
                [$this->callback($callback)],
                [$this->callback($callback)]
            )
            ->willReturnArgument(0);

        $instance->execute();
    }

    public function testExecuteEmptyQueue()
    {
        $instance = $this->getInstance();

        $this->queue->expects($this->atLeastOnce())
            ->method('pull')
            ->willReturn([]);

        $this->registry->expects($this->never())
            ->method($this->anything());

        $callback = function (WorkerEvent $event) use ($instance) {
            self::assertSame($instance, $event->getWorker());

            return true;
        };

        $this->dispatcher->expects($this->exactly(2))
            ->method('dispatch')
            ->withConsecutive(
                [$this->callback($callback)],
                [$this->callback($callback)]
            )
            ->willReturnArgument(0);

        $instance->execute();
    }

    public function testExecuteNoHandler()
    {
        $instance = $this->getInstance();

        $this->queue->expects($this->atLeastOnce())
            ->method('pull')
            ->willReturnOnConsecutiveCalls(
                [$message = $this->getMessage(new stdClass())],
                [$this->getMessage($task = new stdClass())],
                []
            );

        $callback = function ($argument) use ($task) {
            self::assertSame($task, $argument);
        };

        $this->registry->expects($this->exactly(2))
            ->method('getHandler')
            ->with($this->equalTo('stdClass'))
            ->willReturnOnConsecutiveCalls($this->throwException($exception = new Exception()), $callback);

        $callback = [
            function (WorkerEvent $event) use ($instance) {
                self::assertSame($instance, $event->getWorker());

                return true;
            },
            function (ErrorEvent $event) use ($instance, $message, $exception) {
                self::assertSame($instance, $event->getWorker());
                self::assertSame($message, $event->getMessage());
                self::assertSame($exception, $event->getException());

                return true;
            },
        ];

        $this->dispatcher->expects($this->exactly(3))
            ->method('dispatch')
            ->withConsecutive(
                [$this->callback($callback[0])],
                [$this->callback($callback[1])],
                [$this->callback($callback[0])]
            )
            ->willReturnArgument(0);

        $instance->execute();
    }

    /**
     * @dataProvider executeTasksSizeProvider
     */
    public function testExecuteTasksSize($count)
    {
        $instance = $this->getInstance();
        $instance->setOption('tasks', $count);

        $this->queue->expects($this->exactly($count))
            ->method('pull')
            ->willReturn($count ? [$this->getMessage(new stdClass())] : [null]);

        $this->registry->expects($this->exactly($count))
            ->method('getHandler')
            ->with($this->equalTo('stdClass'))
            ->willReturn(function () {
            });

        $instance->execute();
    }

    public function executeTasksSizeProvider()
    {
        return [
            'zero' => [0],
            'one' => [1],
            'ten' => [10],
        ];
    }

    /**
     * @return Worker
     */
    protected function getInstance()
    {
        $instance = new Worker($this->registry, $this->queue);

        if ($this->dispatcher) {
            $instance->setEventDispatcher($this->dispatcher);
        }

        return $instance;
    }

    /**
     * @param mixed $task
     *
     * @return QueueMessageInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getMessage($task)
    {
        $mock = $this->createMock(QueueMessageInterface::class);

        $mock->expects($this->any())
            ->method('getPayload')
            ->willReturn($task);

        $mock->expects($this->atLeastOnce())
            ->method('delete');

        return $mock;
    }
}
