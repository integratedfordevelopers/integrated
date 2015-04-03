<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Doctrine\ODM\MongoDB\Mapping;

use Integrated\Doctrine\ODM\MongoDB\Mapping\ClassTreeMapResolver;
use Integrated\Doctrine\ODM\MongoDB\Mapping\Locator\ClassLocatorInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ClassTreeMapResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ClassLocatorInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $locator;

    protected function setUp()
    {
        $this->locator = $this->getMock('Integrated\\Doctrine\\ODM\\MongoDB\\Mapping\\Locator\\ClassLocatorInterface');
    }

    protected function setUpLocator()
    {
        $this->locator->expects($this->once())
            ->method('getClassNames')
            ->willReturn([
                'Integrated\\Tests\\Doctrine\\ODM\\MongoDB\\Mapping\\TestClass',
                'Integrated\\Tests\\Doctrine\\ODM\\MongoDB\\Mapping\\TestChild4',
                'Integrated\\Tests\\Doctrine\\ODM\\MongoDB\\Mapping\\TestChild3',
                'Integrated\\Tests\\Doctrine\\ODM\\MongoDB\\Mapping\\TestChild2',
                'Integrated\\Tests\\Doctrine\\ODM\\MongoDB\\Mapping\\TestChild1',
                'Integrated\\Tests\\Doctrine\\ODM\\MongoDB\\Mapping\\TestRoot2',
                'Integrated\\Tests\\Doctrine\\ODM\\MongoDB\\Mapping\\TestRoot1',
                'Integrated\\Tests\\Doctrine\\ODM\\MongoDB\\Mapping\\TestBase',
            ]);
    }

    public function testResolveRoot()
    {
        $this->setUpLocator();

        $expected = [
            'Integrated\\Tests\\Doctrine\\ODM\\MongoDB\\Mapping\\TestChild1' => 'Integrated\\Tests\\Doctrine\\ODM\\MongoDB\\Mapping\\TestChild1',
            'Integrated\\Tests\\Doctrine\\ODM\\MongoDB\\Mapping\\TestRoot1'  => 'Integrated\\Tests\\Doctrine\\ODM\\MongoDB\\Mapping\\TestRoot1',
        ];

        self::assertEquals($expected, $this->getInstance()->resolve('Integrated\\Tests\\Doctrine\\ODM\\MongoDB\\Mapping\\TestRoot1'));
    }

    public function testResolveChild()
    {
        $this->setUpLocator();

        $expected = [
            'Integrated\\Tests\\Doctrine\\ODM\\MongoDB\\Mapping\\TestChild4' => 'Integrated\\Tests\\Doctrine\\ODM\\MongoDB\\Mapping\\TestChild4',
            'Integrated\\Tests\\Doctrine\\ODM\\MongoDB\\Mapping\\TestChild3' => 'Integrated\\Tests\\Doctrine\\ODM\\MongoDB\\Mapping\\TestChild3',
            'Integrated\\Tests\\Doctrine\\ODM\\MongoDB\\Mapping\\TestChild2' => 'Integrated\\Tests\\Doctrine\\ODM\\MongoDB\\Mapping\\TestChild2',
            'Integrated\\Tests\\Doctrine\\ODM\\MongoDB\\Mapping\\TestRoot2'  => 'Integrated\\Tests\\Doctrine\\ODM\\MongoDB\\Mapping\\TestRoot2',
        ];

        $resolver = $this->getInstance();

        self::assertEquals($expected, $resolver->resolve('Integrated\\Tests\\Doctrine\\ODM\\MongoDB\\Mapping\\TestChild4'));
        self::assertEquals($expected, $resolver->resolve('Integrated\\Tests\\Doctrine\\ODM\\MongoDB\\Mapping\\TestChild3'));
        self::assertEquals($expected, $resolver->resolve('Integrated\\Tests\\Doctrine\\ODM\\MongoDB\\Mapping\\TestChild2'));
    }

    public function testResolveNotInRoot()
    {
        $this->locator->expects($this->never())
            ->method($this->anything());

        $resolver = $this->getInstance();

        self::assertNull($resolver->resolve('Integrated\\Tests\\Doctrine\\ODM\\MongoDB\\Mapping\\TestBase'));
        self::assertNull($resolver->resolve('Integrated\\Tests\\Doctrine\\ODM\\MongoDB\\Mapping\\TestClass'));
    }

    /**
     * @return ClassTreeMapResolver
     */
    protected function getInstance()
    {
        return new ClassTreeMapResolver($this->locator, ['Integrated\\Tests\\Doctrine\\ODM\\MongoDB\\Mapping\\TestRoot1', 'Integrated\\Tests\Doctrine\\ODM\\MongoDB\\Mapping\\TestRoot2']);
    }
}

class TestBase {}
class TestRoot1 extends TestBase {}
class TestRoot2 extends TestBase {}
class TestChild1 extends TestRoot1 {}
class TestChild2 extends TestRoot2 {}
class TestChild3 extends TestRoot2 {}
class TestChild4 extends TestRoot2 {}
class TestClass {}
