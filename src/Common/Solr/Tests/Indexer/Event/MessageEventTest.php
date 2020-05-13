<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr\Tests\Indexer\Event;

use Integrated\Common\Queue\QueueMessageInterface;
use Integrated\Common\Solr\Indexer\Event\MessageEvent;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class MessageEventTest extends AbstractEventTest
{
    /**
     * @var QueueMessageInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $message;

    public function setUp(): void
    {
        parent::setUp();

        $this->message = $this->createMock(QueueMessageInterface::class);
    }

    public function testGetMessage()
    {
        self::assertSame($this->message, $this->getInstance()->getMessage());
    }

    /**
     * @return MessageEvent
     */
    protected function getInstance()
    {
        return new MessageEvent($this->indexer, $this->message);
    }
}
