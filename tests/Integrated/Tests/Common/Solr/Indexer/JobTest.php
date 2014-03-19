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

use Integrated\Common\Solr\Indexer\Job;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class JobTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var Job
	 */
	protected $job;

	protected function setUp()
	{
		$this->job = new Job();
	}

	public function testInterface()
	{
		$this->assertInstanceOf('Integrated\Common\Solr\Indexer\JobInterface', $this->job);
	}

	public function testConstructAction()
	{
		$this->job = new Job('action');
		$this->assertEquals('action', $this->job->getAction());
	}

	public function testConstructOptions()
	{
		$this->job = new Job(null, ['name1' => 'value', 'name2' => 42]);
		$this->assertSame(['name1' => 'value', 'name2' => '42'], $this->job->getOptions());
	}

	public function testSerialize()
	{
		$this->assertNotEmpty(serialize($this->job));
	}

	public function testUnserialize()
	{
		$this->job->setAction('action');
		$this->job->setOption('name', 'value');

		$this->job = unserialize(serialize($this->job));

		$this->assertEquals('action', $this->job->getAction());
		$this->assertSame(['name' => 'value'], $this->job->getOptions());
	}

	public function testSetAction()
	{
		$this->job->setAction('action');
		$this->assertEquals('action', $this->job->getAction());
	}

	public function testSetActionNull()
	{
		$this->job->setAction('action');
		$this->job->setAction(null);

		$this->assertNull($this->job->getAction());
	}

	public function testSetActionNoneString()
	{
		$this->job->setAction(42);
		$this->assertSame('42', $this->job->getAction());
	}

	public function testGetAction()
	{
		$this->assertNull($this->job->getAction());
	}

	public function testHasAction()
	{
		$this->assertFalse($this->job->hasAction());
		$this->job->setAction('action');
		$this->assertTrue($this->job->hasAction());
		$this->job->setAction(null);
		$this->assertFalse($this->job->hasAction());
	}

	public function testSetOption()
	{
		$this->job->setOption('name', 'value1');
		$this->assertEquals('value1', $this->job->getOption('name'));
		$this->job->setOption('name', 'value2');
		$this->assertEquals('value2', $this->job->getOption('name'));
	}

	public function testSetOptionNoneString()
	{
		$this->job->setOption('name', 42);
		$this->assertSame('42', $this->job->getOption('name'));
	}

	public function testGetOption()
	{
		$this->assertNull($this->job->getOption('name'));
	}

	public function testHasOption()
	{
		$this->assertFalse($this->job->hasOption('name'));
		$this->job->setOption('name', 'value');
		$this->assertTrue($this->job->hasOption('name'));
		$this->job->removeOption('name');
		$this->assertFalse($this->job->hasOption('name'));
	}

	public function testRemoveOption()
	{
		$this->job->setOption('name', 'value');
		$this->job->removeOption('name');

		$this->assertNull($this->job->getOption('name'));
	}

	public function testGetOptions()
	{
		$this->assertSame([], $this->job->getOptions());

		$this->job->setOption('name1', 'value');
		$this->job->setOption('name2', 'value');

		$this->assertSame(['name1' => 'value', 'name2' => 'value'], $this->job->getOptions());
	}

	public function testClearOptions()
	{
		$this->job->setOption('name1', 'value');
		$this->job->setOption('name2', 'value');
		$this->job->clearOptions();

		$this->assertSame([], $this->job->getOptions());
	}
}
 