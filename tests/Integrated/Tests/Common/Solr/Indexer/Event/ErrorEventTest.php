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

use Integrated\Common\Solr\Indexer\Event\ErrorEvent;
use Integrated\Common\Solr\Exception\ExceptionInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ErrorEventTest extends AbstractEventTest
{
	/**
	 * @var ExceptionInterface | \PHPUnit_Framework_MockObject_MockObject
	 */
	protected $exception;

	/**
	 * @var ErrorEvent
	 */
	protected $event;

	public function setUp()
	{
		parent::setUp();

		$this->exception = $this->getMock('Integrated\Common\Solr\Exception\ExceptionInterface');
		$this->event = new ErrorEvent($this->indexer, $this->exception);
	}

	public function testGetException()
	{
		$this->assertSame($this->exception, $this->event->getException());
	}
}
 