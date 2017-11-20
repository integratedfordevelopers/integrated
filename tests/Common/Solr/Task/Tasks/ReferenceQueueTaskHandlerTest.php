<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\Solr\Task\Tasks;

use Integrated\Common\Content\ContentInterface;
use Integrated\Common\Solr\Indexer\JobFactory;
use Integrated\Common\Solr\Task\Provider\ContentProviderInterface;
use Integrated\Common\Solr\Task\Tasks\ReferenceQueueTask;
use Integrated\Common\Solr\Task\Tasks\ReferenceQueueTaskHandler;
use Integrated\Common\Queue\QueueInterface;
use stdClass;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ReferenceQueueTaskHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContentProviderInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $provider;

    /**
     * @var QueueInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $queue;

    /**
     * @var JobFactory | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $factory;

    protected function setUp()
    {
        $this->provider = $this->createMock(ContentProviderInterface::class);
        $this->queue = $this->createMock(QueueInterface::class);
        $this->factory = $this->getMockBuilder(JobFactory::class)->disableOriginalConstructor()->getMock();
    }

    public function testInvoke()
    {
        $content1 = $this->createMock(ContentInterface::class);
        $content2 = $this->createMock(ContentInterface::class);
        $content3 = new stdClass();
        $content4 = $this->createMock(ContentInterface::class);

        $this->provider->expects($this->once())
            ->method('getReferenced')
            ->with($this->equalTo('content-id'))
            ->willReturn([$content1, $content2, $content3, $content4]);

        $job1 = new stdClass();
        $job2 = new stdClass();
        $job3 = new stdClass();

        $this->factory->expects($this->exactly(3))
            ->method('create')
            ->withConsecutive(
                [
                    $this->equalTo(JobFactory::ADD),
                    $this->identicalTo($content1)
                ],
                [
                    $this->equalTo(JobFactory::ADD),
                    $this->identicalTo($content2)
                ],
                [
                    $this->equalTo(JobFactory::ADD),
                    $this->identicalTo($content4)
                ]
            )
            ->willReturnOnConsecutiveCalls($job1, $job2, $job3);

        $this->queue->expects($this->exactly(3))
            ->method('push')
            ->withConsecutive([$this->identicalTo($job1)], [$this->identicalTo($job2)], [$this->identicalTo($job3)]);

        $instance = $this->getInstance();
        $instance->__invoke($this->getTask('content-id'));
    }

    public function testInvokeNoReferencesFound()
    {
        $this->provider->expects($this->once())
            ->method('getReferenced')
            ->with($this->equalTo('content-id'))
            ->willReturn([]);

        $this->factory->expects($this->never())
            ->method($this->anything());

        $this->queue->expects($this->never())
            ->method($this->anything());

        $instance = $this->getInstance();
        $instance->__invoke($this->getTask('content-id'));
    }

    /**
     * @return ReferenceQueueTaskHandler
     */
    protected function getInstance()
    {
        return new ReferenceQueueTaskHandler($this->provider, $this->queue, $this->factory);
    }

    /**
     * @param string $id
     * @return ReferenceQueueTask | \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getTask($id)
    {
        $mock = $this->getMockBuilder(ReferenceQueueTask::class)->disableOriginalConstructor()->getMock();
        $mock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($id);

        return $mock;
    }

    /**
     * @return ContentInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getContent()
    {
        return $this->createMock(ContentInterface::class);
    }
}
