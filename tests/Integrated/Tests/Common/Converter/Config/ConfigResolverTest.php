<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\Converter\Config;

use Integrated\Common\Converter\Config\ConfigResolver;
use Integrated\Common\Converter\Config\TypeConfigInterface;
use Integrated\Common\Converter\Config\TypeProviderInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ConfigResolverTest extends \PHPUnit_Framework_TestCase
{
    protected $CONFIG_INTERFACE = 'Integrated\\Common\\Converter\\Config\\ConfigInterface';

    /**
     * @var TypeProviderInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $provider;

    protected function setUp()
    {
        $this->provider = $this->getMock('Integrated\\Common\\Converter\\Config\\TypeProviderInterface');
    }

    public function testInterface()
    {
        self::assertInstanceOf('Integrated\\Common\\Converter\\Config\\ConfigResolverInterface', $this->getInstance());
    }

    public function testGetConfig()
    {
        $resolver = $this->getInstance();

        $this->provider->expects($this->once())
            ->method('getTypes')
            ->with($this->equalTo('Integrated\\Tests\\Common\\Converter\\Config\\TestClass'))
            ->willReturn([$this->getType()]);

        $config = $resolver->getConfig('Integrated\\Tests\\Common\\Converter\\Config\\TestClass');

        self::assertInstanceOf($this->CONFIG_INTERFACE, $config);
        self::assertFalse($config->hasParent());
        self::assertSame($config, $resolver->getConfig('Integrated\\Tests\\Common\\Converter\\Config\\TestClass'));
    }

    public function testGetConfigParent()
    {
        $resolver = $this->getInstance();

        $this->provider->expects($this->exactly(2))
            ->method('getTypes')
            ->withConsecutive(
                [$this->equalTo('Integrated\\Tests\\Common\\Converter\\Config\\TestParent')],
                [$this->equalTo('Integrated\\Tests\\Common\\Converter\\Config\\TestChild')]
            )
            ->willReturnOnConsecutiveCalls(
                [$this->getType()],
                []
            );

        $config = $resolver->getConfig('Integrated\\Tests\\Common\\Converter\\Config\\TestChild');

        self::assertInstanceOf($this->CONFIG_INTERFACE, $config);
        self::assertFalse($config->hasParent());
        self::assertSame($config, $resolver->getConfig('Integrated\\Tests\\Common\\Converter\\Config\\TestParent'));
    }

    public function testGetConfigParentAndChild()
    {
        $resolver = $this->getInstance();

        $this->provider->expects($this->exactly(2))
            ->method('getTypes')
            ->withConsecutive(
                [$this->equalTo('Integrated\\Tests\\Common\\Converter\\Config\\TestParent')],
                [$this->equalTo('Integrated\\Tests\\Common\\Converter\\Config\\TestChild')]
            )
            ->willReturnOnConsecutiveCalls(
                [$this->getType()],
                [$this->getType()]
            );

        $config = $resolver->getConfig('Integrated\\Tests\\Common\\Converter\\Config\\TestChild');

        self::assertInstanceOf($this->CONFIG_INTERFACE, $config);
        self::assertTrue($config->hasParent());
        self::assertSame($config->getParent(), $resolver->getConfig('Integrated\\Tests\\Common\\Converter\\Config\\TestParent'));
    }

    public function testGetConfigNothingFound()
    {
        $this->provider->expects($this->atLeastOnce())
            ->method('getTypes')
            ->willReturn([]);

        self::assertNull($this->getInstance()->getConfig('Integrated\\Tests\\Common\\Converter\\Config\\TestClass'));
    }

    public function testGetConfigLowerAndUpperCaseClassName()
    {
        $resolver = $this->getInstance();

        $this->provider->expects($this->once())
            ->method('getTypes')
            ->with($this->equalTo('Integrated\\Tests\\Common\\Converter\\Config\\TestClass'))
            ->willReturn([$this->getType()]);

        self::assertSame(
            $resolver->getConfig('integrated\\tests\\common\\converter\\config\\testclass'),
            $resolver->getConfig('INTEGRATED\\TESTS\\COMMON\\CONVERTER\\CONFIG\\TESTCLASS')
        );
    }

    /**
     * @expectedException \Integrated\Common\Converter\Exception\ExceptionInterface
     */
    public function testGetConfigInvalidArgument()
    {
        $this->getInstance()->getConfig(42);
    }

    public function testGetConfigInvalidClass()
    {
        self::assertNull($this->getInstance()->getConfig('Integrated\\Tests\\Common\\Converter\\Config\\DoesNotExist'));
    }

    /**
     * @return ConfigResolver
     */
    protected function getInstance()
    {
        return new ConfigResolver($this->provider);
    }

    /**
     * @return TypeConfigInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getType()
    {
        return $this->getMock('Integrated\\Common\\Converter\\Config\\TypeConfigInterface');
    }
}

class TestParent {}
class TestChild extends TestParent {}
class TestClass {}
