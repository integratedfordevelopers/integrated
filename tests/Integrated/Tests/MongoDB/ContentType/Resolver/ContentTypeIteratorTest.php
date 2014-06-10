<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\MongoDB\ContentType\Resolver;

use Integrated\MongoDB\ContentType\Resolver\ContentTypeIterator;
use Doctrine\ODM\MongoDB\Cursor;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ContentTypeIteratorTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var ContentTypeIterator
	 */
	protected $iterator;

	protected function setUp()
	{
		$this->iterator = new ContentTypeIterator(['key1' => 'data1', 'key2' => 'data2']);
	}

	public function testInterface()
	{
		$this->assertInstanceOf('Integrated\Common\ContentType\ContentTypeIteratorInterface', $this->iterator);
	}

	public function testCurrent()
	{
		$this->assertSame('data1', $this->iterator->current());
		$this->assertSame('data1', $this->iterator->current()); // should still be same value
	}

	public function testNext()
	{
		$this->iterator->next();
		$this->assertSame('data2', $this->iterator->current());
	}

	public function testKey()
	{
		$this->assertEquals('key1', $this->iterator->key());
		$this->iterator->next();
		$this->assertEquals('key2', $this->iterator->key());
	}

	public function testValid()
	{
		$this->assertTrue($this->iterator->valid());
		$this->iterator->next();
		$this->assertTrue($this->iterator->valid());
		$this->iterator->next();
		$this->assertFalse($this->iterator->valid());
	}

	public function testRewind()
	{
		$this->iterator->next();
		$this->iterator->rewind();

		$this->assertSame('data1', $this->iterator->current());
	}
}
 