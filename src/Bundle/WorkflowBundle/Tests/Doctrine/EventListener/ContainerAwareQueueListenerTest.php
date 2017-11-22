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

use Integrated\Bundle\WorkflowBundle\Doctrine\EventListener\ContainerAwareQueueListener;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ContainerAwareQueueListenerTest extends QueueListenerTest
{
    const SERVICE_NAME = 'the.queue.service.name';

    /**
   	 * @var ContainerInterface | \PHPUnit_Framework_MockObject_MockObject
   	 */
   	protected $container;

    protected function setUp()
   	{
        parent::setUp();

   		$this->container = $this->getMock('Symfony\\Component\\DependencyInjection\\ContainerInterface');
        $this->container->expects($this->any())
            ->method('get')
            ->with($this->equalTo(self::SERVICE_NAME))
            ->willReturn($this->queue);
   	}

    public function testSetGetQueue()
    {
        $this->container = $this->getMock('Symfony\\Component\\DependencyInjection\\ContainerInterface');
        $this->container->expects($this->atLeastOnce())
            ->method('get')
            ->with($this->equalTo(self::SERVICE_NAME))
            ->willReturn($this->queue);

        $listener = $this->getInstance();
        $mock = $this->getMock('Integrated\\Common\\Queue\\QueueInterface');

        $this->assertSame($this->queue, $listener->getQueue());
        $listener->setQueue($mock);
        $this->assertSame($mock, $listener->getQueue());
    }

    /**
   	 * @return ContainerAwareQueueListener
   	 */
   	protected function getInstance()
   	{
   		return new ContainerAwareQueueListener($this->container, self::SERVICE_NAME);
   	}
}