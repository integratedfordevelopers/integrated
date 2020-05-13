<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Doctrine\ODM\Tests\MongoDB\Mapping;

use Integrated\Doctrine\ODM\MongoDB\Mapping\ClassTreeMapResolver;
use Integrated\Doctrine\ODM\MongoDB\Mapping\Locator\ClassLocatorInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ClassTreeMapResolverTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ClassLocatorInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $locator;

    protected function setUp(): void
    {
        $this->locator = $this->createMock('Integrated\\Doctrine\\ODM\\MongoDB\\Mapping\\Locator\\ClassLocatorInterface');
    }

    protected function setUpLocator()
    {
        $this->locator->expects($this->once())
            ->method('getClassNames')
            ->willReturn([
                Fixtures\TestClass::class,
                Fixtures\TestChild4::class,
                Fixtures\TestChild3::class,
                Fixtures\TestChild2::class,
                Fixtures\TestChild1::class,
                Fixtures\TestRoot2::class.
                Fixtures\TestRoot1::class,
                Fixtures\TestBase::class,
            ]);
    }

    public function testResolveRoot()
    {
        $this->setUpLocator();

        $expected = [
            Fixtures\TestChild1::class => Fixtures\TestChild1::class,
        ];

        self::assertEquals($expected, $this->getInstance()->resolve(Fixtures\TestRoot1::class));
    }

    public function testResolveChild()
    {
        $this->setUpLocator();

        $expected = [
            Fixtures\TestChild4::class => Fixtures\TestChild4::class,
            Fixtures\TestChild3::class => Fixtures\TestChild3::class,
            Fixtures\TestChild2::class => Fixtures\TestChild2::class,
        ];

        $resolver = $this->getInstance();

        self::assertEquals($expected, $resolver->resolve(Fixtures\TestChild4::class));
        self::assertEquals($expected, $resolver->resolve(Fixtures\TestChild3::class));
        self::assertEquals($expected, $resolver->resolve(Fixtures\TestChild2::class));
    }

    public function testResolveNotInRoot()
    {
        $this->locator->expects($this->never())
            ->method($this->anything());

        $resolver = $this->getInstance();

        self::assertNull($resolver->resolve(Fixtures\TestBase::class));
        self::assertNull($resolver->resolve(Fixtures\TestClass::class));
    }

    /**
     * @return ClassTreeMapResolver
     */
    protected function getInstance()
    {
        return new ClassTreeMapResolver($this->locator, [Fixtures\TestRoot1::class, Fixtures\TestRoot2::class]);
    }
}
