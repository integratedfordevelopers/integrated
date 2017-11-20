<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\Solr\Indexer\Event;

use Integrated\Common\Solr\Indexer\Event\BatchEvent;
use Integrated\Common\Solr\Indexer\BatchOperation;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class BatchEventTest extends AbstractEventTest
{
    /**
     * @var BatchOperation | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $operation;

    public function setUp()
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
