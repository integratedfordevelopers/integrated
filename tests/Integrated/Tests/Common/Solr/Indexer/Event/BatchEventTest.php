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

	/**
	 * @var BatchEvent
	 */
	protected $event;

	public function setUp()
	{
		parent::setUp();

		$this->operation = $this->getMock('Integrated\Common\Solr\Indexer\BatchOperation', array(), array(), '', false);
		$this->event = new BatchEvent($this->indexer, $this->operation);
	}

	public function testGetOperation()
	{
		$this->assertSame($this->operation, $this->event->getOperation());
	}
}
 