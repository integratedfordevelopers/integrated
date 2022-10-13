<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WorkflowBundle\Tests\Extension;

use Integrated\Bundle\WorkflowBundle\Extension\EventListener\ContentSubscriber;
use Integrated\Bundle\WorkflowBundle\Extension\WorkflowExtension;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class WorkflowExtensionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ContentSubscriber|\PHPUnit_Framework_MockObject_MockObject
     */
    private $subscriber;

    protected function setUp(): void
    {
        $this->subscriber = $this->createMock(ContentSubscriber::class);
    }

    public function testInterface()
    {
        $instance = $this->getInstance();

        $this->assertInstanceOf('Integrated\\Common\\Content\\Extension\\ExtensionInterface', $instance);
    }

    public function testGetName()
    {
        $this->assertEquals('integrated.extension.workflow', $this->getInstance()->getName());
    }

    public function testGetSubscribers()
    {
        $subscribers = $this->getInstance()->getSubscribers();

        $this->assertCount(2, $subscribers);
        $this->assertContainsOnlyInstancesOf('Integrated\\Common\\Content\\Extension\\EventSubscriberInterface', $subscribers);
    }

    protected function getInstance()
    {
        return new WorkflowExtension($this->subscriber);
    }
}
