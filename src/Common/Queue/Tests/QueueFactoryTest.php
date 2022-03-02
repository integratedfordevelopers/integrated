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
use Integrated\Common\Queue\QueueFactory;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class QueueFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var QueueFactory
     */
    protected $factory;

    /**
     * @var QueueProviderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $provider;

    protected function setUp(): void
    {
        $this->provider = $this->createMock('Integrated\Common\Queue\Provider\QueueProviderInterface');
        $this->factory = new QueueFactory($this->provider);
    }

    public function testInterface()
    {
        $this->assertInstanceOf('Integrated\Common\Queue\QueueFactoryInterface', $this->factory);
    }

    public function testGetQueue()
    {
        $this->assertInstanceOf('Integrated\Common\Queue\QueueInterface', $this->factory->getQueue('channel'));
    }

    public function getGetQueueRegistry()
    {
        $queue1 = $this->factory->getQueue('channel');
        $queue2 = $this->factory->getQueue('channel');

        $this->assertSame($queue1, $queue2);
    }

    public function getGetQueueRegistryDifferent()
    {
        $queue1 = $this->factory->getQueue('channel1');
        $queue2 = $this->factory->getQueue('channel2');

        $this->assertNotSame($queue1, $queue2);
    }
}
