<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\Solr\Indexer;

use Integrated\Common\Solr\Indexer\BatchOperation;
use Integrated\Common\Queue\QueueMessageInterface;

use Solarium\QueryType\Update\Query\Command\Command;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class BatchOperationTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var BatchOperation
	 */
	protected $operation;

	/**
	 * @var QueueMessageInterface | \PHPUnit_Framework_MockObject_MockObject
	 */
	protected $message;

	/**
	 * @var Command | \PHPUnit_Framework_MockObject_MockObject
	 */
	protected $command;

	protected function setUp()
	{
		$this->message = $this->getMock('Integrated\Common\Queue\QueueMessageInterface');
		$this->command = $this->getMock('Solarium\QueryType\Update\Query\Command\Command');

		$this->operation = new BatchOperation($this->message, $this->command);
	}

	public function testConstructorNullCommand()
	{
		$this->operation = new BatchOperation($this->message);
		$this->assertNull($this->operation->getCommand());
	}

	public function testGetMessage()
	{
		$this->assertSame($this->message, $this->operation->getMessage());
	}

	public function testGetCommand()
	{
		$this->assertSame($this->command, $this->operation->getCommand());
	}

	public function testSetCommand()
	{
		$command = $this->getMock('Solarium\QueryType\Update\Query\Command\Command');

		$this->operation->setCommand($command);

		$this->assertSame($command, $this->operation->getCommand());
	}

	public function testSetCommandNull()
	{
		$this->operation->setCommand(null);
		$this->assertNull($this->operation->getCommand());
	}
}
 