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

use Integrated\Common\ContentType\Exception\InvalidArgumentException;
use Integrated\Common\Form\Mapping\MetadataFactoryInterface;
use Integrated\Common\ContentType\ResolverInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class FormFactoryTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var ResolverInterface | \PHPUnit_Framework_MockObject_MockObject
	 */
	private $resolver;

    /**
     * @var MetadataFactoryInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $metadata;

	protected function setUp()
	{
		$this->resolver = $this->getMock('Integrated\\Common\\ContentType\\ResolverInterface');
        $this->metadata = $this->getMock('Integrated\\Common\\Form\\Mapping\MetadataFactoryInterface');
	}

	public function testInterface()
	{
		$this->assertInstanceOf('Integrated\\Common\\Content\\Form\\FormFactoryInterface', $this->getInstance());
	}

    public function testSetAndGetEventDispatcher()
    {
        $dispatcher = $this->getMock('Symfony\\Component\\EventDispatcher\\EventDispatcherInterface');

        $factory = $this->getInstance();
        $factory->setEventDispatcher($dispatcher);

        self::assertSame($dispatcher, $factory->getEventDispatcher());
    }

    public function testGetEventDispatcherDefault()
    {
        self::assertInstanceOf('Symfony\\Component\\EventDispatcher\\EventDispatcherInterface', $this->getInstance()->getEventDispatcher());
    }

    /**
     * @dataProvider getTypeProvider
     */
    public function testGetType($argument)
    {
        $type = $this->getMock('Integrated\\Common\\ContentType\\ContentTypeInterface');
        $type->expects($this->once())
            ->method('getClass')
            ->willReturn('Integrated\\Common\\ContentType\\ContentTypeInterface');

        $this->resolver->expects($this->once())
            ->method('getType')
            ->with($this->equalTo('content-type'))
            ->willReturn($type);

        $this->metadata->expects($this->once())
            ->method('getMetadata')
            ->with($this->equalTo('Integrated\\Common\\ContentType\\ContentTypeInterface'))
            ->willReturn($this->getMock('Integrated\\Common\\Form\\Mapping\\MetadataInterface'));

        self::assertInstanceOf('Integrated\\Common\\Content\\Form\\FormType', $this->getInstance()->getType($argument));
    }

    public function getTypeProvider()
    {
        $content = $this->getMock('Integrated\\Common\\Content\\ContentInterface');
        $content->expects($this->atLeastOnce())
            ->method('getContentType')
            ->willReturn('content-type');

        return [
            ['content-type'],
            [$content]
        ];
    }

    /**
     * @expectedException \Integrated\Common\Content\Exception\ExceptionInterface
     */
    public function testGetTypeNoString()
    {
        $this->resolver->expects($this->never())
            ->method($this->anything());

        $this->metadata->expects($this->never())
            ->method($this->anything());

        $this->getInstance()->getType(['not a string']);
    }

    /**
     * @expectedException \Integrated\Common\ContentType\Exception\ExceptionInterface
     */
    public function testGetTypeNotFound()
    {
        $this->resolver->expects($this->atLeastOnce())
            ->method('getType')
            ->willThrowException(new InvalidArgumentException());

        $this->metadata->expects($this->never())
            ->method($this->anything());

        $this->getInstance()->getType('not found');
    }

    /**
     * @return FormFactory
     */
    protected function getInstance()
    {
        return new FormFactory($this->resolver, $this->metadata);
    }
}
 