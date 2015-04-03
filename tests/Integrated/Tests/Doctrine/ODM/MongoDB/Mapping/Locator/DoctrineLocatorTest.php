<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Doctrine\ODM\MongoDB\Mapping\Locator;

use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;

use Integrated\Doctrine\ODM\MongoDB\Mapping\Locator\DoctrineLocator;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class DoctrineLocatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MappingDriver | \PHPUnit_Framework_MockObject_MockObject
     */
    private $driver;

    protected function setUp()
    {
        $this->driver = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\Driver\\MappingDriver');
    }

    public function testInterface()
    {
        self::assertInstanceOf('Integrated\\Doctrine\\ODM\\MongoDB\\Mapping\\Locator\\ClassLocatorInterface', $this->getInstance());
    }

    public function testGetClassNames()
    {
        $classes = [
            'namespace\\class1',
            'namespace\\class2',
            'namespace\\class3',
        ];

        $this->driver->expects($this->once())
            ->method('getAllClassNames')
            ->willReturn($classes);

        self::assertEquals($classes, $this->getInstance()->getClassNames());
    }

    /**
     * @return DoctrineLocator
     */
    protected function getInstance()
    {
        return new DoctrineLocator($this->driver);
    }
}
 