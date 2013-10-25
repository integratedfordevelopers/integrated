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

use Integrated\Common\ContentType\Resolver\ContentTypePriorityResolver;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ContentTypePriorityResolverTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var ContentTypePriorityResolver
	 */
	private $resolver;

	protected function setUp()
	{
		$this->resolver = new ContentTypePriorityResolver();
	}

	public function testInterface()
	{
		$this->assertInstanceOf('Integrated\Common\ContentType\Resolver\ContentTypeResolverInterface', $this->resolver);
	}

	public function testInitialState()
	{
		$this->assertEquals(array(), $this->resolver->getResolvers());
	}

	public function testAddResolver()
	{
		$resolver1 = $this->getMock('Integrated\Common\ContentType\Resolver\ContentTypeResolverInterface');
		$resolver2 = $this->getMock('Integrated\Common\ContentType\Resolver\ContentTypeResolverInterface');

		$this->resolver->addResolver($resolver1);

		$this->assertTrue($this->resolver->hasResolver($resolver1));
		$this->assertFalse($this->resolver->hasResolver($resolver2));
	}

	public function testRemoveResolver()
	{
		$resolver1 = $this->getMock('Integrated\Common\ContentType\Resolver\ContentTypeResolverInterface');
		$resolver2 = $this->getMock('Integrated\Common\ContentType\Resolver\ContentTypeResolverInterface');

		$this->resolver->addResolver($resolver1);
		$this->resolver->addResolver($resolver2);
		$this->resolver->removeResolver($resolver1);

		$this->assertFalse($this->resolver->hasResolver($resolver1));
		$this->assertTrue($this->resolver->hasResolver($resolver2));
	}

	public function testGetResolvers()
	{
		$resolver1 = $this->getMock('Integrated\Common\ContentType\Resolver\ContentTypeResolverInterface');
		$resolver2 = $this->getMock('Integrated\Common\ContentType\Resolver\ContentTypeResolverInterface');

		$this->resolver->addResolver($resolver1);
		$this->resolver->addResolver($resolver1);
		$this->resolver->addResolver($resolver2);

		$this->assertCount(2, $this->resolver->getResolvers());
		$this->assertContainsOnlyInstancesOf('Integrated\Common\ContentType\Resolver\ContentTypeResolverInterface', $this->resolver->getResolvers());
	}

	public function testGetResolversPriority()
	{
		$resolver1 = $this->getMock('Integrated\Common\ContentType\Resolver\ContentTypeResolverInterface');
		$resolver2 = $this->getMock('Integrated\Common\ContentType\Resolver\ContentTypeResolverInterface');
		$resolver3 = $this->getMock('Integrated\Common\ContentType\Resolver\ContentTypeResolverInterface');

		$this->resolver->addResolver($resolver1, 10);
		$this->resolver->addResolver($resolver2, 10);
		$this->resolver->addResolver($resolver3, 10);

		$this->assertSame(array($resolver1, $resolver2, $resolver3), $this->resolver->getResolvers());

		$this->resolver->addResolver($resolver2, 10);
		$this->resolver->addResolver($resolver3, 20);

		$this->assertSame(array($resolver3, $resolver1, $resolver2), $this->resolver->getResolvers());
	}

	public function testClearResolvers()
	{
		$this->resolver->addResolver($this->getMock('Integrated\Common\ContentType\Resolver\ContentTypeResolverInterface'));
		$this->resolver->addResolver($this->getMock('Integrated\Common\ContentType\Resolver\ContentTypeResolverInterface'));
		$this->resolver->clearResolvers();

		$this->assertEquals(array(), $this->resolver->getResolvers());
	}

	public function testGetType()
	{
		$type1 = $this->getMock('Integrated\Common\ContentType\ContentTypeInterface');

		$resolver1 = $this->getMock('Integrated\Common\ContentType\Resolver\ContentTypeResolverInterface');
		$resolver1->expects($this->once())
			->method('hasType')
			->with($this->identicalTo('class'), $this->identicalTo('type'))
			->will($this->returnValue(true));

		$resolver1->expects($this->once())
			->method('getType')
			->with($this->identicalTo('class'), $this->identicalTo('type'))
			->will($this->returnValue($type1));

		$this->resolver->addResolver($resolver1);

		$this->assertSame($type1, $this->resolver->getType('class', 'type'));
	}

	public function testGetTypePriority()
	{
		$type1 = $this->getMock('Integrated\Common\ContentType\ContentTypeInterface');
		$type2 = $this->getMock('Integrated\Common\ContentType\ContentTypeInterface');

		$resolver1 = $this->getMock('Integrated\Common\ContentType\Resolver\ContentTypeResolverInterface');
		$resolver1->expects($this->any())
			->method('hasType')
			->with($this->identicalTo('class'), $this->identicalTo('type'))
			->will($this->returnValue(true));

		$resolver1->expects($this->any())
			->method('getType')
			->with($this->identicalTo('class'), $this->identicalTo('type'))
			->will($this->returnValue($type1));

		$resolver2 = $this->getMock('Integrated\Common\ContentType\Resolver\ContentTypeResolverInterface');
		$resolver2->expects($this->any())
			->method('hasType')
			->with($this->identicalTo('class'), $this->identicalTo('type'))
			->will($this->returnValue(true));

		$resolver2->expects($this->any())
			->method('getType')
			->with($this->identicalTo('class'), $this->identicalTo('type'))
			->will($this->returnValue($type2));

		$this->resolver->addResolver($resolver1);
		$this->resolver->addResolver($resolver2, 10);

		$this->assertSame($type2, $this->resolver->getType('class', 'type'));
	}

	/**
	 * @expectedException \Integrated\Common\ContentType\Exception\ExceptionInterface
	 */
	public function testGetTypeNoResolver()
	{
		$this->resolver->getType('class', 'type');
	}

	/**
	 * @expectedException \Integrated\Common\ContentType\Exception\ExceptionInterface
	 */
	public function testGetTypeNotFound()
	{
		$resolver1 = $this->getMock('Integrated\Common\ContentType\Resolver\ContentTypeResolverInterface');
		$resolver1->expects($this->once())
			->method('hasType')
			->will($this->returnValue(false));

		$this->resolver->addResolver($resolver1);
		$this->resolver->getType('class', 'type');
	}

	public function testHasType()
	{
		$resolver1 = $this->getMock('Integrated\Common\ContentType\Resolver\ContentTypeResolverInterface');
		$resolver1->expects($this->once())
			->method('hasType')
			->with($this->identicalTo('class'), $this->identicalTo('type'))
			->will($this->returnValue(true));

		$resolver2 = $this->getMock('Integrated\Common\ContentType\Resolver\ContentTypeResolverInterface');
		$resolver2->expects($this->never())
			->method('hasType')
			->will($this->returnValue(false));

		$this->resolver->addResolver($resolver1);
		$this->resolver->addResolver($resolver2);

		$this->assertTrue($this->resolver->hasType('class', 'type'));
	}

	public function testHasTypeNoResolver()
	{
		$this->assertFalse($this->resolver->hasType('class', 'type'));
	}

	public function testHasTypeNotFound()
	{
		$resolver1 = $this->getMock('Integrated\Common\ContentType\Resolver\ContentTypeResolverInterface');
		$resolver1->expects($this->once())
			->method('hasType')
			->will($this->returnValue(false));

		$this->resolver->addResolver($resolver1);

		$this->assertFalse($this->resolver->hasType('class', 'type'));
	}
}