<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr\Tests\Task\Event;

use Exception;
use Integrated\Common\Queue\QueueMessageInterface;
use Integrated\Common\Solr\Task\Event\ErrorEvent;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ErrorEventTest extends WorkerEventTest
{
    /**
     * @var Exception | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $exception;

    /**
     * @var QueueMessageInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $message;

    protected function setUp(): void
    {
        parent::setUp();

        $this->message = $this->createMock(QueueMessageInterface::class);
        $this->exception = $this->createMock(Exception::class);
    }

    public function testGetMessage()
    {
        self::assertSame($this->message, $this->getInstance()->getMessage());
    }

    public function testGetException()
    {
        self::assertSame($this->exception, $this->getInstance()->getException());
    }

    /**
     * @return ErrorEvent
     */
    protected function getInstance()
    {
        return new ErrorEvent($this->worker, $this->message, $this->exception);
    }
}
