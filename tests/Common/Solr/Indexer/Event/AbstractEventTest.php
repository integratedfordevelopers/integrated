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

use Integrated\Common\Solr\Indexer\Event\IndexerEvent;
use Integrated\Common\Solr\Indexer\IndexerInterface;

use Symfony\Component\EventDispatcher\Event;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
abstract class AbstractEventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IndexerInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $indexer;

    public function setUp()
    {
        $this->indexer = $this->createMock(IndexerInterface::class);
    }

    public function testParent()
    {
        self::assertInstanceOf(Event::class, $this->getInstance());
    }

    public function testGetIndexer()
    {
        self::assertSame($this->indexer, $this->getInstance()->getIndexer());
    }

    /**
     * @return IndexerEvent
     */
    abstract protected function getInstance();
}
