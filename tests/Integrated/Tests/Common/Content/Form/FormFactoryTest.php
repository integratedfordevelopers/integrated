<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\Content\Form;

use Integrated\Common\Content\Form\FormFactory;
use Integrated\Common\ContentType\Resolver\ContentTypeResolverInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class FormFactoryTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var ContentTypeResolverInterface | \PHPUnit_Framework_MockObject_MockObject
	 */
	private $resolver;

	/**
	 * @var FormFactory
	 */
	private $factory;

	protected function setUp()
	{
		$this->resolver = $this->getMock('Integrated\Common\ContentType\Resolver\ContentTypeResolverInterface');
		$this->factory = new FormFactory($this->resolver);

	}

	public function testInterface()
	{
		$this->assertInstanceOf('Integrated\Common\Content\Form\FormFactoryInterface', $this->factory);
	}

	public function testGetType()
	{
		$class = $this->getMockClass('Integrated\Common\Content\ContentInterface');

		$this->resolver->expects($this->once())
			->method('getType')
			->with($this->identicalTo($class), $this->identicalTo('type'))
			->will($this->returnValue($this->getMock('Integrated\Common\ContentType\ContentTypeInterface')));

		$this->assertInstanceOf('Integrated\Common\Content\Form\FormType', $this->factory->getType($class, 'type'));
	}

	/**
	 * @expectedException \Integrated\Common\Content\Exception\UnexpectedTypeException
	 */
	public function testGetTypeNoType()
	{
		$this->factory->getType($this->getMockClass('Integrated\Common\Content\ContentInterface'));
	}

	public function testGetTypeWithContentType()
	{
		$content = $this->getMock('Integrated\Common\Content\ContentInterface');

		$this->resolver->expects($this->once())
			->method('getType')
			->with($this->identicalTo(get_class($content)), $this->identicalTo('type'))
			->will($this->returnValue($this->getMock('Integrated\Common\ContentType\ContentTypeInterface')));

		$this->assertInstanceOf('Integrated\Common\Content\Form\FormType', $this->factory->getType($content, 'type'));
	}

	public function testGetTypeWithContentTypeAndNoType()
	{
		$content = $this->getMock('Integrated\Common\Content\ContentInterface');
		$content->expects($this->once())
			->method('getContentType')
			->will($this->returnValue('type'));

		$this->resolver->expects($this->once())
			->method('getType')
			->with($this->identicalTo(get_class($content)), $this->identicalTo('type'))
			->will($this->returnValue($this->getMock('Integrated\Common\ContentType\ContentTypeInterface')));

		$this->assertInstanceOf('Integrated\Common\Content\Form\FormType', $this->factory->getType($content));
	}

	/**
	 * @expectedException \Integrated\Common\Content\Exception\UnexpectedTypeException
	 */
	public function testGetTypeInvalidClass()
	{
		$this->factory->getType(10, 'type');
	}

	/**
	 * @expectedException \Integrated\Common\Content\Exception\UnexpectedTypeException
	 */
	public function testGetTypeInvalidType()
	{
		$this->factory->getType($this->getMockClass('Integrated\Common\Content\ContentInterface'), 10);
	}

	/**
	 * @expectedException \Integrated\Common\Content\Exception\InvalidArgumentException
	 */
	public function testGetTypeStringNotSubClass()
	{
		$this->factory->getType(new \stdClass());
	}

	/**
	 * @expectedException \Integrated\Common\Content\Exception\InvalidArgumentException
	 */
	public function testGetTypeObjectNotSubClass()
	{
		$this->factory->getType('stdClass');
	}
}
 