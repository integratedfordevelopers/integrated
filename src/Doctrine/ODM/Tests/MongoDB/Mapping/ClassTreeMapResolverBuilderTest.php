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

use Integrated\Doctrine\ODM\MongoDB\Mapping\ClassTreeMapResolverBuilder;
use Integrated\Doctrine\ODM\MongoDB\Mapping\Locator\ClassLocatorInterface;
use ReflectionClass;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ClassTreeMapResolverBuilderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ClassLocatorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $locator;

    protected function setUp(): void
    {
        $this->locator = $this->createMock('Integrated\\Doctrine\\ODM\\MongoDB\\Mapping\\Locator\\ClassLocatorInterface');
    }

    public function testAddClass()
    {
        $builder = $this->getInstance();

        $builder->addClass('class1');
        $builder->addClass('class2');

        $reflection = new ReflectionClass('Integrated\\Doctrine\\ODM\\MongoDB\\Mapping\\ClassTreeMapResolver');

        $prop = $reflection->getProperty('map_roots');
        $prop->setAccessible(true);

        self::assertSame(['class1', 'class2'], $prop->getValue($builder->getResolver()));
    }

    public function testConstructor()
    {
        $reflection = new ReflectionClass('Integrated\\Doctrine\\ODM\\MongoDB\\Mapping\\ClassTreeMapResolver');

        $prop = $reflection->getProperty('locator');
        $prop->setAccessible(true);

        self::assertSame($this->locator, $prop->getValue($this->getInstance()->getResolver()));
    }

    /**
     * @return ClassTreeMapResolverBuilder
     */
    protected function getInstance()
    {
        return new ClassTreeMapResolverBuilder($this->locator);
    }
}
