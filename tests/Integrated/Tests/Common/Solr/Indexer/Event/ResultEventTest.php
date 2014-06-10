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

	/**
	 * @var ResultEvent
	 */
	protected $event;

	public function setUp()
	{
		parent::setUp();

		$this->result = $this->getMock('Solarium\Core\Query\Result\ResultInterface');
		$this->event = new ResultEvent($this->indexer, $this->result);
	}

	public function testGetResult()
	{
		$this->assertSame($this->result, $this->event->getResult());
	}
}
