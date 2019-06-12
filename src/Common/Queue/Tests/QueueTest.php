<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Queue\Tests;

use Integrated\Common\Queue\Provider\QueueProviderInterface;
use Integrated\Common\Queue\Queue;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class QueueTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Queue
     */
    protected $queue;

    /**
     * @var QueueProviderInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $provider;

    protected function setUp()
    {
        $this->provider = $this->createMock('Integrated\Common\Queue\Provider\QueueProviderInterface');
        $this->queue = new Queue($this->provider, 'channel');
    }

    public function testInterface()
    {
        $this->assertInstanceOf('Integrated\Common\Queue\QueueInterface', $this->queue);
    }

    public function testPriorityConstants()
    {
        $this->assertEquals(-10, Queue::PRIORITY_LOW);
        $this->assertEquals(0, Queue::PRIORITY_MEDIUM);
        $this->assertEquals(10, Queue::PRIORITY_HIGH);
    }

    public function testGetChannel()
    {
        $this->assertEquals('channel', $this->queue->getChannel());
    }

    public function testGetProvider()
    {
        $this->assertSame($this->provider, $this->queue->getProvider());
    }

    public function testPush()
    {
        $payload = new \stdClass();

        $this->provider->expects($this->once())
            ->method('push')
            ->with($this->identicalTo('channel'), $this->identicalTo($payload), $this->identicalTo(0), $this->identicalTo(0));

        $this->queue->push($payload);
    }

    public function testPushWithDelay()
    {
        $payload = new \stdClass();

        $this->provider->expects($this->once())
            ->method('push')
            ->with($this->identicalTo('channel'), $this->identicalTo($payload), $this->identicalTo(42), $this->identicalTo(0));

        $this->queue->push($payload, 42);
    }

    public function testPushWithPriority()
    {
        $payload = new \stdClass();

        $this->provider->expects($this->once())
            ->method('push')
            ->with($this->identicalTo('channel'), $this->identicalTo($payload), $this->identicalTo(0), $this->identicalTo(10));

        $this->queue->push($payload, 0, Queue::PRIORITY_HIGH);
    }

    public function testPull()
    {
        $message = $this->createMock('Integrated\Common\Queue\QueueMessageInterface');

        $this->provider->expects($this->once())
            ->method('pull')
            ->with($this->identicalTo('channel'), $this->identicalTo(1))
            ->willReturn([$message]);

        $this->assertSame([$message], $this->queue->pull());
    }

    public function testPullWithLimit()
    {
        $message1 = $this->createMock('Integrated\Common\Queue\QueueMessageInterface');
        $message2 = $this->createMock('Integrated\Common\Queue\QueueMessageInterface');

        $this->provider->expects($this->once())
            ->method('pull')
            ->with($this->identicalTo('channel'), $this->identicalTo(42))
            ->willReturn([$message1, $message2]);

        $this->assertSame([$message1, $message2], $this->queue->pull(42));
    }

    public function testCount()
    {
        $this->provider->expects($this->once())
            ->method('count')
            ->with($this->identicalTo('channel'))
            ->willReturn(42);

        $this->assertEquals(42, $this->queue->count());
    }

    public function testClear()
    {
        $this->provider->expects($this->once())
            ->method('clear')
            ->with($this->identicalTo('channel'));

        $this->queue->clear();
    }
}
