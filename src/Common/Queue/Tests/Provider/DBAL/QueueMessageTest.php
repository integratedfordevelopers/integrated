<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Queue\Tests\Provider\DBAL;

use Integrated\Common\Queue\Provider\DBAL\QueueMessage;
use stdClass;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class QueueMessageTest extends \PHPUnit\Framework\TestCase
{
    public const PAYLOAD = 'O:8:"stdClass":0:{}'; // serialized stdClass;

    protected $data;

    protected function setUp(): void
    {
        $this->data = [
            'id' => 'ThisIsTheID',
            'payload' => self::PAYLOAD,
            'attempts' => '42',
        ];
    }

    public function testInterface()
    {
        $message = new QueueMessage($this->data, function () {
        }, function () {
        });

        $this->assertInstanceOf('Integrated\Common\Queue\QueueMessageInterface', $message);
    }

    public function testGetPayload()
    {
        $message = new QueueMessage($this->data, function () {
        }, function () {
        });

        $this->assertInstanceOf('stdClass', $message->getPayload());
    }

    public function testGetPayloadCached()
    {
        $message = new QueueMessage($this->data, function () {
        }, function () {
        });

        $this->assertSame($message->getPayload(), $message->getPayload());
    }

    public function testGetAttempts()
    {
        $message = new QueueMessage($this->data, function () {
        }, function () {
        });

        $this->assertSame(42, $message->getAttempts());
    }

    public function testGetId()
    {
        $message = new QueueMessage($this->data, function () {
        }, function () {
        });

        $this->assertSame('ThisIsTheID', $message->getId());
    }

    public function testGetData()
    {
        $message = new QueueMessage($this->data, function () {
        }, function () {
        });

        $this->assertSame($this->data, $message->getData());
    }

    public function testRelease()
    {
        $delete = $this->getMockBuilder('stdClass')->addMethods(['callback'])->getMock();
        $delete->expects($this->never())
            ->method('callback');

        $release = $this->getMockBuilder('stdClass')->addMethods(['callback'])->getMock();
        $release->expects($this->once())
            ->method('callback')
            ->with($this->identicalTo(0));

        $message = new QueueMessage($this->data, function () use ($delete) {
            $delete->callback();
        }, function ($delay) use ($release) {
            $release->callback($delay);
        });

        $message->release();
        $message->release();

        $message->delete();
    }

    public function testReleaseWithDelay()
    {
        $release = $this->getMockBuilder('stdClass')->addMethods(['callback'])->getMock();
        $release->expects($this->once())
            ->method('callback')
            ->with($this->identicalTo(42));

        $message = new QueueMessage($this->data, function () {
        }, function ($delay) use ($release) {
            $release->callback($delay);
        });
        $message->release(42);
    }

    public function testDelete()
    {
        $delete = $this->getMockBuilder('stdClass')->addMethods(['callback'])->getMock();
        $delete->expects($this->once())
            ->method('callback');

        $release = $this->getMockBuilder('stdClass')->addMethods(['callback'])->getMock();
        $release->expects($this->never())
            ->method('callback');

        $message = new QueueMessage($this->data, function () use ($delete) {
            $delete->callback();
        }, function () use ($release) {
            $release->callback();
        });

        $message->delete();
        $message->release();
    }
}
