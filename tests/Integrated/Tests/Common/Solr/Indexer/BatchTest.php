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

use Integrated\Common\Solr\Indexer\Batch;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class BatchTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var Batch
	 */
	protected $batch;

	protected function setUp()
	{
		$this->batch = new Batch();
	}

	public function testAdd()
	{
		$operation = $this->getMock('Integrated\Common\Solr\Indexer\BatchOperation', array(), array(), '', false);

		$this->batch->add($operation);

		$this->assertSame([$operation], iterator_to_array($this->batch));
	}

	public function testAddOrder()
	{
		$operation1 = $this->getMock('Integrated\Common\Solr\Indexer\BatchOperation', array(), array(), '', false);
		$operation2 = $this->getMock('Integrated\Common\Solr\Indexer\BatchOperation', array(), array(), '', false);

		$this->batch->add($operation1);
		$this->batch->add($operation2);

		$this->assertSame([$operation1, $operation2], iterator_to_array($this->batch));
	}

	public function testRemove()
	{
		$operation1 = $this->getMock('Integrated\Common\Solr\Indexer\BatchOperation', array(), array(), '', false);
		$operation2 = $this->getMock('Integrated\Common\Solr\Indexer\BatchOperation', array(), array(), '', false);
		$operation3 = $this->getMock('Integrated\Common\Solr\Indexer\BatchOperation', array(), array(), '', false);

		$this->batch->add($operation1);
		$this->batch->add($operation2);
		$this->batch->add($operation3);

		$this->batch->remove($operation2);

		$this->assertSame([$operation1, $operation3], iterator_to_array($this->batch));

		$this->batch->remove($operation1);
		$this->batch->remove($operation3);

		$this->assertEmpty(iterator_to_array($this->batch));
	}

	public function testClear()
	{
		$this->batch->add($this->getMock('Integrated\Common\Solr\Indexer\BatchOperation', array(), array(), '', false));
		$this->batch->add($this->getMock('Integrated\Common\Solr\Indexer\BatchOperation', array(), array(), '', false));

		$this->batch->clear();

		$this->assertEquals(0, $this->batch->count());
	}

	public function testCount()
	{
		$this->assertInstanceOf('Countable', $this->batch);
		$this->assertEquals(0, $this->batch->count());

		$this->batch->add($this->getMock('Integrated\Common\Solr\Indexer\BatchOperation', array(), array(), '', false));

		$this->assertEquals(1, $this->batch->count());

		$this->batch->add($this->getMock('Integrated\Common\Solr\Indexer\BatchOperation', array(), array(), '', false));
		$this->batch->add($this->getMock('Integrated\Common\Solr\Indexer\BatchOperation', array(), array(), '', false));

		$this->assertEquals(3, $this->batch->count());
	}
}
 