<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\ContentType\Mapping\Metadata;

use Integrated\Common\ContentType\Mapping\Driver;
use Integrated\Common\ContentType\Mapping\Metadata\ContentTypeFactory;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ContentTypeFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Driver\DriverInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $driver;

    /**
     * @var ContentTypeFactory
     */
    private $factory;

    /**
     * Setup the test
     */
    protected function setUp()
    {
        // Mock Driver\DriverInterface
        $this->driver = $this->getMock('Integrated\Common\ContentType\Mapping\Driver\DriverInterface');

        // Create ContentTypeFactory
        $this->factory = new ContentTypeFactory($this->driver);
    }

    /**
     * Test the build function
     */
    public function testBuildFunction()
    {
        // Stub loadMetadataForClass
        $return = 'Dummy';
        $this->driver->expects($this->once())
            ->method('loadMetadataForClass')
            ->will($this->returnValue($return));

        // Assert
        $this->assertEquals($return, $this->factory->build(__NAMESPACE__ . '\Test'));
        $this->assertEquals($return, $this->factory->build(__NAMESPACE__ . '\Test'));

    }
}

/**
 * Dummy class
 */
class Test {}