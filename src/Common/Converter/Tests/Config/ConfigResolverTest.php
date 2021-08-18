<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Converter\Tests\Config;

use Integrated\Common\Converter\Config\ConfigResolver;
use Integrated\Common\Converter\Config\TypeConfigInterface;
use Integrated\Common\Converter\Config\TypeProviderInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ConfigResolverTest extends \PHPUnit\Framework\TestCase
{
    protected $CONFIG_INTERFACE = 'Integrated\\Common\\Converter\\Config\\ConfigInterface';

    /**
     * @var TypeProviderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $provider;

    protected function setUp(): void
    {
        $this->provider = $this->createMock('Integrated\\Common\\Converter\\Config\\TypeProviderInterface');
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
            ->with($this->equalTo(Fixtures\TestClass::class))
            ->willReturn([$this->getType()]);

        $config = $resolver->getConfig(Fixtures\TestClass::class);

        self::assertInstanceOf($this->CONFIG_INTERFACE, $config);
        self::assertFalse($config->hasParent());
        self::assertSame($config, $resolver->getConfig(Fixtures\TestClass::class));
    }

    public function testGetConfigParent()
    {
        $resolver = $this->getInstance();

        $this->provider->expects($this->exactly(2))
            ->method('getTypes')
            ->withConsecutive(
                [$this->equalTo(Fixtures\TestParent::class)],
                [$this->equalTo(Fixtures\TestChild::class)]
            )
            ->willReturnOnConsecutiveCalls(
                [$this->getType()],
                []
            );

        $config = $resolver->getConfig(Fixtures\TestChild::class);

        self::assertInstanceOf($this->CONFIG_INTERFACE, $config);
        self::assertFalse($config->hasParent());
        self::assertSame($config, $resolver->getConfig(Fixtures\TestParent::class));
    }

    public function testGetConfigParentAndChild()
    {
        $resolver = $this->getInstance();

        $this->provider->expects($this->exactly(2))
            ->method('getTypes')
            ->withConsecutive(
                [$this->equalTo(Fixtures\TestParent::class)],
                [$this->equalTo(Fixtures\TestChild::class)]
            )
            ->willReturnOnConsecutiveCalls(
                [$this->getType()],
                [$this->getType()]
            );

        $config = $resolver->getConfig(Fixtures\TestChild::class);

        self::assertInstanceOf($this->CONFIG_INTERFACE, $config);
        self::assertTrue($config->hasParent());
        self::assertSame($config->getParent(), $resolver->getConfig(Fixtures\TestParent::class));
    }

    public function testGetConfigNothingFound()
    {
        $this->provider->expects($this->atLeastOnce())
            ->method('getTypes')
            ->willReturn([]);

        self::assertNull($this->getInstance()->getConfig(Fixtures\TestChild::class));
    }

    public function testGetConfigLowerAndUpperCaseClassName()
    {
        $resolver = $this->getInstance();

        $this->provider->expects($this->once())
            ->method('getTypes')
            ->with($this->equalTo(Fixtures\TestClass::class))
            ->willReturn([$this->getType()]);

        self::assertSame(
            $resolver->getConfig('integrated\\common\\converter\\tests\\config\\fixtures\\testclass'),
            $resolver->getConfig('INTEGRATED\\COMMON\\CONVERTER\\TESTS\\CONFIG\\FIXTURES\\TESTCLASS')
        );
    }

    public function testGetConfigInvalidArgument()
    {
        $this->expectException(\Integrated\Common\Converter\Exception\ExceptionInterface::class);

        $this->getInstance()->getConfig(42);
    }

    public function testGetConfigInvalidClass()
    {
        self::assertNull($this->getInstance()->getConfig('Integrated\\Tests\\Common\\Converter\\Config\\Fixtures\\DoesNotExist'));
    }

    /**
     * @return ConfigResolver
     */
    protected function getInstance()
    {
        return new ConfigResolver($this->provider);
    }

    /**
     * @return TypeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getType()
    {
        return $this->createMock('Integrated\\Common\\Converter\\Config\\TypeConfigInterface');
    }
}
