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

use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Integrated\Bundle\ChannelBundle\EventListener\Doctrine\ChannelDistributionListener;
use Integrated\Bundle\ChannelBundle\Tests\EventListener\Mock\Serializer;
use Integrated\Bundle\ContentBundle\Document\Channel\Channel;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Common\Channel\Exporter\Queue\Request;
use Integrated\Common\Queue\Provider\Memory\QueueProvider;
use Integrated\Common\Queue\Queue;
use Integrated\Common\Queue\QueueMessageInterface;
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

    /**
     */
    protected function setUp()
    {
        $this->queue = new Queue(new QueueProvider(), 'test');
        $this->listener = new ChannelDistributionListener($this->queue, new Serializer());
    }

    /**
     */
    public function testQueueState()
    {
        $document = $this->createMock(Content::class);

        $document
            ->method('getChannels')
            ->willReturn([new Channel()]);

        $document
            ->method('isPublished')
            ->willReturnOnConsecutiveCalls(false, true);

        $event = $this->createMock(LifecycleEventArgs::class);

        $event
            ->method('getDocument')
            ->willReturn($document);

        self::assertCount(0, $this->queue);

        $this->listener->postUpdate($event);
        $this->listener->postUpdate($event);

        self::assertSame('delete', $this->getPayload()->state);
        self::assertSame('add', $this->getPayload()->state);
    }

    /**
     * Get the first payload from the queue.
     *
     * @return Request
     */
    private function getPayload()
    {
        $documents = $this->queue->pull(1);
        $document = reset($documents);

        self::assertCount(1, $documents);
        self::assertInstanceOf(QueueMessageInterface::class, $document);

        $payload = $document->getPayload();

        self::assertInstanceOf(Request::class, $payload);

        return $payload;
    }
}
