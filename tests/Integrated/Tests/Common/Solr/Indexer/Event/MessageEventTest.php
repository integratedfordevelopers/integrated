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

use Integrated\Common\Solr\Indexer\Event\MessageEvent;
use Integrated\Common\Queue\QueueMessageInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class MessageEventTest extends AbstractEventTest
{
	/**
	 * @var QueueMessageInterface | \PHPUnit_Framework_MockObject_MockObject
	 */
	protected $message;

	/**
	 * @var MessageEvent
	 */
	protected $event;

	public function setUp()
	{
		parent::setUp();

		$this->message = $this->getMock('Integrated\Common\Queue\QueueMessageInterface');
		$this->event = new MessageEvent($this->indexer, $this->message);
	}

	public function testGetMessage()
	{
		$this->assertSame($this->message, $this->event->getMessage());
	}
}
