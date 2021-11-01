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

use Integrated\Common\Solr\Exception\ExceptionInterface;
use Integrated\Common\Solr\Indexer\Event\ErrorEvent;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ErrorEventTest extends MessageEventTest
{
    /**
     * @var ExceptionInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $exception;

    protected function setUp(): void
    {
        parent::setUp();

        $this->exception = $this->createMock(ExceptionInterface::class);
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
        return new ErrorEvent($this->indexer, $this->message, $this->exception);
    }
}
