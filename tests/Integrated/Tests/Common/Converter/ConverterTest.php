<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\Converter;

use Integrated\Common\Converter\Config\ConfigInterface;
use Integrated\Common\Converter\Config\ConfigResolverInterface;
use Integrated\Common\Converter\Config\TypeConfigInterface;

use Integrated\Common\Converter\ContainerFactoryInterface;
use Integrated\Common\Converter\ContainerInterface;
use Integrated\Common\Converter\Converter;

use Integrated\Common\Converter\Type\RegistryInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RegistryInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $registry;

    /**
     * @var ConfigResolverInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $resolver;

    /**
     * @var ContainerFactoryInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $factory;

    protected function setUp()
    {
        $this->registry = $this->getMock('Integrated\\Common\\Converter\\Type\\RegistryInterface');
        $this->resolver = $this->getMock('Integrated\\Common\\Converter\\Config\\ConfigResolverInterface');
        $this->factory  = $this->getMock('Integrated\\Common\\Converter\\ContainerFactoryInterface');
    }

    public function testInterface()
    {
        self::assertInstanceOf('Integrated\\Common\\Converter\\ConverterInterface', $this->getInstance());
    }

    public function testConvert()
    {
        $container = $this->getContainer();

        $this->factory->expects($this->once())
            ->method('createContainer')
            ->willReturn($container);

        $this->resolver->expects($this->once())
            ->method('getConfig')
            ->with($this->equalTo('Integrated\\Tests\\Common\\Converter\\TestClass'))
            ->willReturn($this->getConfig([$this->getType('type-1', null), $this->getType('type-2', ['options'])]));

        $data = new TestClass();

        $type1 = $this->getMock('Integrated\\Common\\Converter\\Type\\ResolvedTypeInterface');
        $type1->expects($this->once())
            ->method('build')
            ->with($this->identicalTo($container), $this->identicalTo($data), $this->equalTo([]));

        $type2 = $this->getMock('Integrated\\Common\\Converter\\Type\\ResolvedTypeInterface');
        $type2->expects($this->once())
            ->method('build')
            ->with($this->identicalTo($container), $this->identicalTo($data), $this->equalTo(['options']));

        $this->registry->expects($this->exactly(2))
            ->method('getType')
            ->willReturnMap([
                ['type-1', $type1],
                ['type-2', $type2]
            ]);

        self::assertSame($container, $this->getInstance()->convert($data));
    }

    public function testConvertNoConfigFound()
    {
        $container = $this->getContainer();

        $this->factory->expects($this->once())
            ->method('createContainer')
            ->willReturn($container);

        $this->resolver->expects($this->once())
            ->method('getConfig')
            ->with($this->equalTo('Integrated\\Tests\\Common\\Converter\\TestClass'))
            ->willReturn(null);

        $this->registry->expects($this->never())
            ->method($this->anything());

        self::assertSame($container, $this->getInstance()->convert(new TestClass()));
    }

    /**
     * @expectedException \Integrated\Common\Converter\Exception\ExceptionInterface
     */
    public function testConvertTypeNotFound()
    {
        $this->factory->expects($this->once())
            ->method('createContainer')
            ->willReturn($this->getContainer());

        $this->resolver->expects($this->once())
            ->method('getConfig')
            ->with($this->equalTo('Integrated\\Tests\\Common\\Converter\\TestClass'))
            ->willReturn($this->getConfig([$this->getType('does-not-exist')]));

        $this->registry->expects($this->any())
            ->method('getType')
            ->with($this->equalTo('does-not-exist'))
            ->willThrowException($this->getMock('Integrated\\Common\\Converter\\Exception\\RuntimeException'));

        $this->getInstance()->convert(new TestClass());
    }

    /**
     * @expectedException \Integrated\Common\Converter\Exception\ExceptionInterface
     */
    public function testConvertInvalidArgument()
    {
        $this->factory->expects($this->never())
            ->method('createContainer');

        $this->getInstance()->convert(42);
    }

    public function testConvertNullArgument()
    {
        $container = $this->getContainer();

        $this->factory->expects($this->once())
            ->method('createContainer')
            ->willReturn($container);

        self::assertSame($container, $this->getInstance()->convert(null));
    }

    /**
     * @return Converter
     */
    protected function getInstance()
    {
        return new Converter($this->registry, $this->resolver, $this->factory);
    }

    /**
     * @return ContainerInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getContainer()
    {
        $mock = $this->getMock('Integrated\\Common\\Converter\\ContainerInterface');
        $mock->expects($this->never())
            ->method($this->anything()); // the convert self should not nothing with the container

        return $mock;
    }

    /**
     * @param TypeConfigInterface[] $types
     * @return ConfigInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getConfig(array $types)
    {
        $mock = $this->getMock('Integrated\\Common\\Converter\\Config\\ConfigInterface');

        $mock->expects($this->any())
            ->method('hasParent')
            ->willReturn(false);

        $mock->expects($this->any())
            ->method('getParent')
            ->willReturn(null);

        $mock->expects($this->any())
            ->method('getTypes')
            ->willReturn($types);

        return $mock;
    }

    /**
     * @param string $name
     * @param array $options
     *
     * @return TypeConfigInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getType($name, array $options = null)
    {
        $mock = $this->getMock('Integrated\\Common\\Converter\\Config\\TypeConfigInterface');

        $mock->expects($this->any())
            ->method('getName')
            ->willReturn($name);

        $mock->expects($this->any())
            ->method('hasOptions')
            ->willReturn($options !== null ? true : false);

        $mock->expects($this->any())
            ->method('getOptions')
            ->willReturn($options);

        return $mock;
    }
}

class TestClass {}
