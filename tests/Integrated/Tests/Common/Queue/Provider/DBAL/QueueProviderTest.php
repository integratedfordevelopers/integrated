<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\Queue\Provider\DBAL;

use stdClass;

use Integrated\Common\Queue\Provider\DBAL\QueueMessage;
use Integrated\Common\Queue\Provider\DBAL\QueueProvider;

use Doctrine\DBAL\Connection;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class QueueProviderTest extends \PHPUnit_Framework_TestCase
{
	const PAYLOAD = 'O:8:"stdClass":0:{}'; // serialized stdClass;

	/**
	 * @var QueueProvider
	 */
	protected $provider;

	/**
	 * @var Connection | \PHPUnit_Framework_MockObject_MockObject
	 */
	protected $connection;

	protected function setUp()
	{
		$options = array(
			'queue_table_name' => 'queue'
		);

		$this->connection = $this->getMock('Doctrine\DBAL\Connection', array(), array(), '', false);
		$this->provider = new QueueProvider($this->connection, $options);
	}

	public function testInterface()
	{
		$this->assertInstanceOf('Integrated\Common\Queue\Provider\QueueProviderInterface', $this->provider);
	}

	public function testPush()
	{
		$this->connection->expects($this->once())
			->method('insert')
			->with($this->identicalTo('queue'));

		$this->provider->push('channel', new stdClass());
	}

	public function testPull()
	{
//		$this->assert
//
//		$this->assertEquals()
	}

	public function testCount()
	{

	}
}