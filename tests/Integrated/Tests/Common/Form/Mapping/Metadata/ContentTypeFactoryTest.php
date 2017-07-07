<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\Form\Mapping\Metadata;

use Integrated\Common\Form\Mapping\Driver;
use Integrated\Common\Form\Mapping\MetadataFactory;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ContentTypeFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Integrated\Common\Form\Mapping\DriverInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $driver;

    /**
     * @var MetadataFactory
     */
    private $factory;

    /**
     * Setup the test
     */
    protected function setUp()
    {
        // Mock Driver\DriverInterface
        $this->driver = $this->getMock('Integrated\Common\Form\Mapping\DriverInterface');

        // Create ContentTypeFactory
        $this->factory = new MetadataFactory($this->driver);
    }

    /**
     * Test the build function
     */
    public function testBuildFunction()
    {
        $this->markTestSkipped('Factory is refactored');

        // Stub loadMetadataForClass
        $return = 'Dummy';
        $this->driver->expects($this->once())
            ->method('loadMetadataForClass')
            ->will($this->returnValue($return));

        // Assert
        $this->assertEquals($return, $this->factory->getMetadata(\stdClass::class));
        $this->assertEquals($return, $this->factory->getMetadata(\stdClass::class));
    }
}
