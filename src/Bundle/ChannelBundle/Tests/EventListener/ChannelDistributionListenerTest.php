<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ChannelBundle\Tests\EventListener;

use DateTime;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Integrated\Bundle\ChannelBundle\EventListener\Doctrine\ChannelDistributionListener;
use Integrated\Bundle\ChannelBundle\Tests\EventListener\Mock\Serializer;
use Integrated\Bundle\ContentBundle\Document\Channel\Channel;
use Integrated\Common\Channel\Exporter\Queue\Request;
use Integrated\Common\Content\ChannelableInterface;
use Integrated\Common\Content\PublishableInterface;
use Integrated\Common\Content\PublishTimeInterface;
use Integrated\Common\Queue\Memory\QueueMessageInterface;
use Integrated\Common\Queue\Provider\Memory\QueueProvider;
use Integrated\Common\Queue\Queue;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ChannelDistributionListenerTest extends TestCase
{
    /**
     * @var Queue
     */
    private $queue;

    /**
     * @var ChannelDistributionListener
     */
    private $listener;

    protected function setUp()
    {
        $this->queue = new Queue(new QueueProvider(), 'test');
        $this->listener = new ChannelDistributionListener($this->queue, new Serializer());
    }

    public function testQueueWithoutChannels()
    {
        $document = $this->createMock(ChannelableInterface::class);
        $document
            ->method('getChannels')
            ->willReturn([]);

        $this->listener->postPersist($this->getLifecycleEventArgs($document));
        $this->assertCount(0, $this->queue);
    }

    public function testQueuePublishableInterface()
    {
        $document = $this->createMock(ChannelableInterface::class);
        $document
            ->method('getChannels')
            ->willReturn([
                $this->createMock(Channel::class),
            ]);

        $this->listener->postPersist($this->getLifecycleEventArgs($document));

        $this->assertCount(1, $this->queue);
        $this->assertSame('add', $this->getPayload()->state);
    }

    public function testQueueNotPublished()
    {
        $document = $this->getDocument();
        $document
            ->method('isPublished')
            ->willReturn(false);

        $this->listener->postPersist($this->getLifecycleEventArgs($document));

        $this->assertCount(1, $this->queue);
        $this->assertSame('delete', $this->getPayload()->state);
    }

    /**
     * @group time-sensitive
     */
    public function testQueueDelayedStartDate()
    {
        $startDate = DateTime::createFromFormat('U', time())->modify('+1 day');

        $document = $this->getDocumentWithPublishTime($startDate);

        $this->listener->postUpdate($this->getLifecycleEventArgs($document));

        $this->assertCount(1, $this->queue);

        $message = $this->pull();

        $this->assertSame('add', $message->getPayload()->state);
        $this->assertSame($startDate->getTimestamp(), $message->getExecuteAt());
    }

    /**
     * @group time-sensitive
     */
    public function testQueueDelayedEndDate()
    {
        $startDate = DateTime::createFromFormat('U', time());
        $endDate = DateTime::createFromFormat('U', time())->modify('+1 day');

        $document = $this->getDocumentWithPublishTime($startDate, $endDate);

        $this->listener->postUpdate($this->getLifecycleEventArgs($document));

        $this->assertCount(2, $this->queue);

        $message = $this->pull();

        $this->assertSame('add', $message->getPayload()->state);
        $this->assertSame($startDate->getTimestamp(), $message->getExecuteAt());

        $message = $this->pull();

        $this->assertSame('delete', $message->getPayload()->state);
        $this->assertSame($endDate->getTimestamp(), $message->getExecuteAt());
    }

    /**
     * @group time-sensitive
     */
    public function testQueueMaxEndDate()
    {
        $startDate = DateTime::createFromFormat('U', time());
        $endDate = new DateTime(PublishTimeInterface::DATE_MAX); // should not be queued

        $document = $this->getDocumentWithPublishTime($startDate, $endDate);

        $this->listener->postUpdate($this->getLifecycleEventArgs($document));

        $this->assertCount(1, $this->queue);
        $this->assertSame('add', $this->getPayload()->state);
    }

    /**
     * @param MockObject $document
     *
     * @return LifecycleEventArgs|MockObject
     */
    private function getLifecycleEventArgs(MockObject $document): LifecycleEventArgs
    {
        $event = $this->createMock(LifecycleEventArgs::class);
        $event
            ->method('getDocument')
            ->willReturn($document);

        return $event;
    }

    /**
     * @return MockObject
     */
    private function getDocument(): MockObject
    {
        $document = $this->getMockBuilder([
            PublishableInterface::class,
            ChannelableInterface::class,
        ])->getMock();

        $document
            ->method('getChannels')
            ->willReturn([
                $this->createMock(Channel::class),
            ]);

        return $document;
    }

    /**
     * @param DateTime|null $startDate
     * @param DateTime|null $endDate
     *
     * @return MockObject
     */
    private function getDocumentWithPublishTime(DateTime $startDate = null, DateTime $endDate = null): MockObject
    {
        $document = $this->getDocument();
        $document->method('isPublished')
            ->willReturn(true);

        $publishTime = $this->createMock(PublishTimeInterface::class);

        if ($startDate) {
            $publishTime
                ->method('getStartDate')
                ->willReturn($startDate);
        }

        if ($endDate) {
            $publishTime
                ->method('getEndDate')
                ->willReturn($endDate);
        }

        $document
            ->method('getPublishTime')
            ->willReturn($publishTime);

        return $document;
    }

    /**
     * Get the first message from the queue.
     *
     * @return QueueMessageInterface
     */
    private function pull(): QueueMessageInterface
    {
        $documents = $this->queue->pull(1);
        $document = reset($documents);

        $this->assertCount(1, $documents);
        $this->assertInstanceOf(QueueMessageInterface::class, $document);

        return $document;
    }

    /**
     * Get the first payload from the queue.
     *
     * @return Request
     */
    private function getPayload(): Request
    {
        $payload = $this->pull()->getPayload();

        $this->assertInstanceOf(Request::class, $payload);

        return $payload;
    }
}
