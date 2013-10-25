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
	 * @var Cursor | \PHPUnit_Framework_MockObject_MockObject
	 */
	protected $cursor;

	/**
	 * @var ContentTypeIterator
	 */
	protected $iterator;

	protected function setUp()
	{
		$this->cursor = $this->getMock('Doctrine\ODM\MongoDB\Cursor', array(), array(), '', false);
		$this->iterator = new ContentTypeIterator($this->cursor);
	}

	public function testInterface()
	{
		$this->assertInstanceOf('Integrated\Common\ContentType\ContentTypeIteratorInterface', $this->iterator);
	}

	public function testCurrent()
	{
		$type1 = $this->getMock('Integrated\Common\ContentType\ContentTypeInterface');

		$this->cursor->expects($this->once())
			->method('valid')
			->will($this->returnValue(true));

		$this->cursor->expects($this->once())
			->method('current')
			->will($this->returnValue($type1));

		$this->assertSame($type1, $this->iterator->current());
		$this->assertSame($type1, $this->iterator->current()); // second call should not call cursor functions
	}

	public function testNext()
	{
		$this->cursor->expects($this->once())
			->method('next');

		$this->iterator->next();
	}

	public function testKey()
	{
		$this->cursor->expects($this->once())
			->method('key')
			->will($this->returnValue('key'));

		$this->assertEquals('key', $this->iterator->key());
	}

	public function testValid()
	{
		$this->cursor->expects($this->once())
			->method('valid')
			->will($this->returnValue(true));

		$this->assertTrue($this->iterator->valid());
	}

	public function testRewind()
	{
		$this->cursor->expects($this->once())
			->method('rewind');

		$this->iterator->rewind();
	}
}
 