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

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
abstract class AbstractEventTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var IndexerInterface | \PHPUnit_Framework_MockObject_MockObject
	 */
	protected $indexer;

	/**
	 * @var IndexerEvent
	 */
	protected $event;

	public function setUp()
	{
		$this->indexer = $this->getMock('Integrated\Common\Solr\Indexer\IndexerInterface');
	}

	public function testParent()
	{
		$this->assertInstanceOf('Symfony\Component\EventDispatcher\Event', $this->event);
	}

	public function testGetIndexer()
	{
		$this->assertSame($this->indexer, $this->event->getIndexer());
	}
}
