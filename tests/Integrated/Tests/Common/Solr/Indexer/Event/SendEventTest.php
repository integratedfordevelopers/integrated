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

	/**
	 * @var SendEvent
	 */
	protected $event;

	public function setUp()
	{
		parent::setUp();

		$this->query = $this->getMock('Solarium\QueryType\Update\Query\Query');
		$this->event = new SendEvent($this->indexer, $this->query);
	}

	public function testGetQuery()
	{
		$this->assertSame($this->query, $this->event->getQuery());
	}
}
