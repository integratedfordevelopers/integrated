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

use Integrated\MongoDB\ContentType\Resolver\ContentTypeResolver;
use Doctrine\ODM\MongoDB\DocumentRepository;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ContentTypeResolverTest extends \PHPUnit_Framework_TestCase
{
//	private $contentTypeClass;

	/**
	 * @var DocumentRepository | \PHPUnit_Framework_MockObject_MockObject
	 */
	private $repository;

	/**
	 * @var ContentTypeResolver
	 */
	private $resolver;

	protected function setUp()
	{
		$class = $this->getMockClass('Integrated\Common\ContentType\ContentTypeInterface');

		$this->repository = $this->getMock('Doctrine\ODM\MongoDB\DocumentRepository', array(), array(), '', false);
		$this->repository->expects($this->any())
			->method('getClassName')
			->will($this->returnValue($class));

		$this->resolver = new ContentTypeResolver($this->repository);
	}

	public function testInterface()
	{
		$this->assertInstanceOf('Integrated\Common\ContentType\Resolver\ContentTypeResolverListInterface', $this->resolver);
	}

	/**
	 * @expectedException \Integrated\Common\ContentType\Exception\ExceptionInterface
	 */
	public function testInvalidRepository()
	{
		$repository = $this->getMock('Doctrine\ODM\MongoDB\DocumentRepository', array(), array(), '', false);
		$repository->expects($this->any())
			->method('getClassName')
			->will($this->returnValue('stdClass'));

		new ContentTypeResolver($repository);
	}

	public function testGetType()
	{
		$type = $this->getMock('Integrated\Common\ContentType\ContentTypeInterface');

		$this->repository->expects($this->once())
			->method('findOneBy')
			->will($this->returnValue($type));

		$this->assertSame($type, $this->resolver->getType('class', 'type'));
		$this->assertSame($type, $this->resolver->getType('class', 'type')); // second call should return cached version
	}

	/**
	 * @expectedException \Integrated\MongoDB\ContentType\Exception\UnexpectedTypeException
	 */
	public function testGetTypeInvalidClass()
	{
		$this->resolver->getType(10, 'type');
	}

	/**
	 * @expectedException \Integrated\MongoDB\ContentType\Exception\UnexpectedTypeException
	 */
	public function testGetTypeInvalidType()
	{
		$this->resolver->getType('class', false);
	}

	/**
	 * @expectedException \Integrated\Common\ContentType\Exception\ExceptionInterface
	 */
	public function testGetTypeNotFound()
	{
		$this->repository->expects($this->once())
			->method('findOneBy')
			->will($this->returnValue(null));

		$this->resolver->getType('class', 'type');
	}

	public function testHasType()
	{
		$type = $this->getMock('Integrated\Common\ContentType\ContentTypeInterface');

		$this->repository->expects($this->once())
			->method('findOneBy')
			->will($this->returnValue($type));

		$this->assertTrue($this->resolver->hasType('class', 'type'));
	}

	/**
	 * @expectedException \Integrated\MongoDB\ContentType\Exception\UnexpectedTypeException
	 */
	public function testHasTypeInvalidClass()
	{
		$this->resolver->hasType(10, 'type');
	}

	/**
	 * @expectedException \Integrated\MongoDB\ContentType\Exception\UnexpectedTypeException
	 */
	public function testHasTypeInvalidType()
	{
		$this->resolver->hasType('class', false);
	}

	public function testHasTypeNotFound()
	{
		$this->repository->expects($this->once())
			->method('findOneBy')
			->will($this->returnValue(null));

		$this->assertFalse($this->resolver->hasType('class', 'type'));
	}

	public function testGetTypes()
	{
//		$type1 = $this->getMock('Integrated\Common\ContentType\ContentTypeInterface');
//		$type2 = $this->getMock('Integrated\Common\ContentType\ContentTypeInterface');
//		$type3 = $this->getMock('Integrated\Common\ContentType\ContentTypeInterface');

		$this->repository->expects($this->once())
			->method('findAll')
			->will($this->returnValue($this->getMock('Doctrine\ODM\MongoDB\Cursor', array(), array(), '', false)));

//		$iterator = $this->resolver->getTypes();

		$this->assertInstanceOf('Integrated\Common\ContentType\ContentTypeIteratorInterface', $this->resolver->getTypes());
//		$this->assertSame($type1, $iterator->current());
//		$this->assertSame(array($type1, $type2, $type3), iterator_to_array($iterator));
	}
}