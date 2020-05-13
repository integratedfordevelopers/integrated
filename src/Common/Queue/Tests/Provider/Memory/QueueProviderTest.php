<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Queue\Tests\Provider\Memory;

use Integrated\Common\Queue\Provider\Memory\QueueMessage;
use Integrated\Common\Queue\Provider\Memory\QueueProvider;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class QueueProviderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var QueueProvider
     */
    protected $provider;

    protected function setUp(): void
    {
        $this->provider = new QueueProvider();
    }

    public function testInterface()
    {
        $this->assertInstanceOf('Integrated\Common\Queue\Provider\QueueProviderInterface', $this->provider);
    }

    public function testPush()
    {
        $this->assertEquals(0, $this->provider->count('channel'));
        $this->provider->push('channel', 'payload');
        $this->assertEquals(1, $this->provider->count('channel'));
        $this->provider->push('channel', 'payload');
        $this->assertEquals(2, $this->provider->count('channel'));
    }

    public function testPull()
    {
        $this->provider->push('channel', 'payload');
        $this->provider->push('channel', 'payload');

        $result = $this->provider->pull('channel');

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertContainsOnlyInstancesOf('Integrated\Common\Queue\Provider\Memory\QueueMessage', $result);
        $this->assertEquals(1, $this->provider->count('channel'));
    }

    public function testPullWithLimit()
    {
        $this->provider->push('channel', 'payload');
        $this->provider->push('channel', 'payload');

        $result = $this->provider->pull('channel', 2);

        $this->assertCount(2, $result);
        $this->assertContainsOnlyInstancesOf('Integrated\Common\Queue\Provider\Memory\QueueMessage', $result);
        $this->assertEquals(0, $this->provider->count('channel'));
    }

    public function testPullWithLimitBiggerThenQueue()
    {
        $this->provider->push('channel', 'payload');
        $this->provider->push('channel', 'payload');

        $result = $this->provider->pull('channel', 4);

        $this->assertCount(2, $result);
        $this->assertEquals(0, $this->provider->count('channel'));
    }

    public function testPullOrder()
    {
        $this->provider->push('channel', 'payload1');
        $this->provider->push('channel', 'payload2');
        $this->provider->push('channel', 'payload3');

        /** @var QueueMessage $message */
        $message = $this->provider->pull('channel');
        $message = array_pop($message);

        $this->assertEquals('payload1', $message->getPayload());
    }

    public function testPullOrderAfterRelease()
    {
        $this->provider->push('channel', 'payload1');
        $this->provider->push('channel', 'payload2');
        $this->provider->push('channel', 'payload3');

        /** @var QueueMessage $message */
        $message = $this->provider->pull('channel');
        $message = array_pop($message);

        $this->provider->pull('channel'); // ignore

        $message->release();

        $message = $this->provider->pull('channel');
        $message = array_pop($message);

        $this->assertEquals('payload1', $message->getPayload());
    }

    public function testPullNoneExistingChannel()
    {
        $result = $this->provider->pull('channel');

        $this->assertIsArray($result);
        $this->assertCount(0, $result);
    }

    public function testRelease()
    {
        $this->provider->push('channel', 'payload');
        $this->provider->push('channel', 'payload');

        /** @var QueueMessage $message */
        $message = $this->provider->pull('channel');
        $message = array_pop($message);

        $this->assertEquals(1, $this->provider->count('channel'));

        $message->release();

        $this->assertEquals(2, $this->provider->count('channel'));
    }

    public function testAttempts()
    {
        $this->provider->push('channel', 'payload');

        /** @var QueueMessage $message */
        $message = $this->provider->pull('channel');
        $message = array_pop($message);

        $this->assertEquals(0, $message->getAttempts());

        $message->release();

        $message = $this->provider->pull('channel');
        $message = array_pop($message);

        $this->assertEquals(1, $message->getAttempts());
    }

    public function testClear()
    {
        $this->provider->push('channel1', 'payload');
        $this->provider->push('channel1', 'payload');
        $this->provider->push('channel2', 'payload');

        $this->provider->clear('channel1');

        $this->assertEmpty($this->provider->pull('channel1'));
        $this->assertNotEmpty($this->provider->pull('channel2'));
    }

    public function testCount()
    {
        $this->provider->push('channel1', 'payload');
        $this->provider->push('channel1', 'payload');
        $this->provider->push('channel2', 'payload');

        $this->assertEquals(2, $this->provider->count('channel1'));
        $this->assertEquals(1, $this->provider->count('channel2'));
    }
}
