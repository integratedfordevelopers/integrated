<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\ContentType\Resolver;

use Integrated\Common\ContentType\Resolver\ContentTypePriorityIterator;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ContentTypePriorityIteratorTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var ContentTypePriorityIterator
	 */
	protected $iterator;

	protected function setUp()
	{
		$this->iterator = new ContentTypePriorityIterator();
	}

	public function testInterface()
	{
		$this->assertInstanceOf('Integrated\Common\ContentType\ContentTypeIteratorInterface', $this->iterator);
	}

	public function testEmpty()
	{
		$this->assertFalse($this->iterator->valid());
		$this->assertNull($this->iterator->key());
		$this->assertNull($this->iterator->current());

		$this->iterator->next();

		$this->assertFalse($this->iterator->valid());
		$this->assertNull($this->iterator->key());
		$this->assertNull($this->iterator->current());
	}

	public function testAppend()
	{
		$this->iterator->append($this->getResolver(array($this->getType('class', 'type'))));

		$this->assertTrue($this->iterator->valid());
	}

	public function testCurrent()
	{
		$type = $this->getType('class', 'type');

		$this->iterator->append($this->getResolver(array($type)));

		$this->assertSame($type, $this->iterator->current());
	}

	public function testNext()
	{
		$type1 = $this->getType('class', 'type1');
		$type2 = $this->getType('class', 'type2');

		$this->iterator->append($this->getResolver(array($type1, $type2)));
		$this->iterator->next();

		$this->assertSame($type2, $this->iterator->current());

		$this->iterator->next();

		$this->assertNull($this->iterator->current());
	}

	public function testNextPriority()
	{
		$type1 = $this->getType('class', 'type1');
		$type2 = $this->getType('class', 'type2');

		$this->iterator->append($this->getResolver(array($type1, $type2)));

		$type3 = $this->getType('class', 'type1');
		$type4 = $this->getType('class', 'type3');

		$this->iterator->append($this->getResolver(array($type3, $type4)));

		$this->assertSame($type1, $this->iterator->current());

		$this->iterator->next();

		$this->assertSame($type2, $this->iterator->current());

		$this->iterator->next();

		$this->assertSame($type4, $this->iterator->current());
	}

	public function testKey()
	{
		$type1 = $this->getType('class', 'type1');
		$type2 = $this->getType('class', 'type2');

		$this->iterator->append($this->getResolver(array($type1, $type2)));

		$this->assertEquals(0, $this->iterator->key());

		$this->iterator->next();

		$this->assertEquals(1, $this->iterator->key());

		$this->iterator->next();

		$this->assertNull($this->iterator->key());
	}

	public function testValid()
	{
		$type = $this->getType('class', 'type');

		$this->iterator->append($this->getResolver(array($type)));

		$this->assertTrue($this->iterator->valid());

		$this->iterator->next();

		$this->assertFalse($this->iterator->valid());
	}

	public function testRewind()
	{
		$type1 = $this->getType('class', 'type1');
		$type2 = $this->getType('class', 'type2');

		$this->iterator->append($this->getResolver(array($type1, $type2)));

		$this->iterator->next();
		$this->iterator->rewind();

		$this->assertSame($type1, $this->iterator->current());
		$this->assertEquals(0, $this->iterator->key());
	}

	/**
	 * @param string $class
	 * @param string $type
	 * @return \Integrated\Common\ContentType\ContentTypeInterface | \PHPUnit_Framework_MockObject_MockObject
	 */
	protected function getType($class, $type)
	{
		$contentType = $this->getMock('Integrated\Common\ContentType\ContentTypeInterface');
		$contentType->expects($this->atLeastOnce())
			->method('getClass')
			->will($this->returnValue($class));

		$contentType->expects($this->atLeastOnce())
			->method('getType')
			->will($this->returnValue($type));

		return $contentType;
	}

	/**
	 * @param \Integrated\Common\ContentType\ContentTypeInterface[] $types
	 * @return \Integrated\Common\ContentType\Resolver\ContentTypeResolverInterface | \PHPUnit_Framework_MockObject_MockObject
	 */
	protected function getResolver(array $types)
	{
		$resolver = $this->getMock('Integrated\Common\ContentType\Resolver\ContentTypeResolverInterface');
		$resolver->expects($this->once())
			->method('getTypes')
			->will($this->returnValue(new \ArrayIterator($types)));

		return $resolver;
	}
}
 