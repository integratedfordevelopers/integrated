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

use Integrated\Common\Solr\Indexer\Event\SendEvent;
use Solarium\QueryType\Update\Query\Query;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class SendEventTest extends AbstractEventTest
{
    /**
     * @var Query | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $query;

    public function setUp(): void
    {
        parent::setUp();

        $this->query = $this->createMock(Query::class);
    }

    public function testGetQuery()
    {
        self::assertSame($this->query, $this->getInstance()->getQuery());
    }

    /**
     * @return SendEvent
     */
    protected function getInstance()
    {
        return new SendEvent($this->indexer, $this->query);
    }
}
