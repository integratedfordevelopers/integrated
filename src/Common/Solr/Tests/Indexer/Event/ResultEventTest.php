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

use Integrated\Common\Solr\Indexer\Event\ResultEvent;
use Solarium\Core\Query\Result\ResultInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ResultEventTest extends AbstractEventTest
{
    /**
     * @var ResultInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $result;

    protected function setUp(): void
    {
        parent::setUp();

        $this->result = $this->createMock(ResultInterface::class);
    }

    public function testGetResult()
    {
        self::assertSame($this->result, $this->getInstance()->getResult());
    }

    protected function getInstance()
    {
        return new ResultEvent($this->indexer, $this->result);
    }
}
