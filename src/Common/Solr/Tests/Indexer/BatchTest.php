<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr\Tests\Indexer;

use Countable;
use Integrated\Common\Solr\Indexer\Batch;
use Integrated\Common\Solr\Indexer\BatchOperation;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class BatchTest extends \PHPUnit\Framework\TestCase
{
    public function testAdd()
    {
        $operation = [
            $this->getOperation(),
            $this->getOperation(),
        ];

        $instance = $this->getInstance();

        $instance->add($operation[0]);
        $instance->add($operation[1]);

        self::assertSame($operation, iterator_to_array($instance));
    }

    public function testRemove()
    {
        $operation = [
            $this->getOperation(),
            $this->getOperation(),
            $this->getOperation(),
        ];

        $instance = $this->getInstance();

        $instance->add($operation[0]);
        $instance->add($operation[1]);
        $instance->add($operation[2]);

        $instance->remove($operation[1]);

        self::assertSame([$operation[0], $operation[2]], iterator_to_array($instance));

        $instance->remove($operation[0]);
        $instance->remove($operation[2]);

        self::assertEmpty(iterator_to_array($instance));
    }

    public function testClear()
    {
        $instance = $this->getInstance();

        $instance->add($this->getOperation());
        $instance->add($this->getOperation());

        $instance->clear();

        self::assertEquals(0, $instance->count());
    }

    public function testCount()
    {
        $instance = $this->getInstance();

        self::assertInstanceOf(Countable::class, $instance);
        self::assertEquals(0, $instance->count());

        $instance->add($this->getOperation());

        self::assertEquals(1, $instance->count());

        $instance->add($this->getOperation());
        $instance->add($this->getOperation());

        self::assertEquals(3, $instance->count());
    }

    /**
     * @return Batch
     */
    protected function getInstance()
    {
        return new Batch();
    }

    /**
     * @return BatchOperation|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getOperation()
    {
        return $this->getMockBuilder(BatchOperation::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
