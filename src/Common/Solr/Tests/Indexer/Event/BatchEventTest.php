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

use Integrated\Common\Solr\Indexer\BatchOperation;
use Integrated\Common\Solr\Indexer\Event\BatchEvent;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class BatchEventTest extends AbstractEventTest
{
    /**
     * @var BatchOperation | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $operation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->operation = $this->getMockBuilder(BatchOperation::class)->disableOriginalConstructor()->getMock();
    }

    public function testGetOperation()
    {
        self::assertSame($this->operation, $this->getInstance()->getOperation());
    }

    /**
     * @return BatchEvent
     */
    protected function getInstance()
    {
        return new BatchEvent($this->indexer, $this->operation);
    }
}
