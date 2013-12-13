<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SolrBundle\Tests\Mapping\Driver\Metadata;

use Integrated\Bundle\SolrBundle\Mapping\Driver\Metadata\DriverChain;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class DriverChainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DriverChain
     */
    private $driver;

    /**
     * Setup the test
     */
    protected function setUp()
    {
        // Create Driver
        $this->driver = new DriverChain(array());
    }

    /**
     * Driver should implement DriverInterface
     */
    public function testInstanceofDriverInterface()
    {
        $this->assertInstanceOf('Integrated\Bundle\SolrBundle\Mapping\Driver\Metadata\DriverInterface', $this->driver);
    }

    /**
     * Test getDrivers function
     */
    public function testGetDriversFunction()
    {
        /* @var $driver \Integrated\Bundle\SolrBundle\Mapping\Driver\Metadata\DriverInterface |\PHPUnit_Framework_MockObject_MockObject */
        $driver = $this->getMock('Integrated\Bundle\SolrBundle\Mapping\Driver\Metadata\DriverInterface');
        $this->driver->addDriver($driver, 'Test');

        $this->assertContains($driver, $this->driver->getDrivers());
    }

    /**
     * Test setDrivers function
     */
    public function testSetDriversFunction()
    {
        /* @var $driver \Integrated\Bundle\SolrBundle\Mapping\Driver\Metadata\DriverInterface |\PHPUnit_Framework_MockObject_MockObject */
        $driver = $this->getMock('Integrated\Bundle\SolrBundle\Mapping\Driver\Metadata\DriverInterface');

        $drivers = array(get_class($driver) => $driver);
        $this->driver->setDrivers($drivers);

        $this->assertSame($drivers, $this->driver->getDrivers());
    }

    /**
     * Test loadMetadataForClass function with one driver
     */
    public function testLoadMetadataForClassFunctionWithOneDriver()
    {
        /* @var $driver \Integrated\Bundle\SolrBundle\Mapping\Driver\Metadata\DriverInterface |\PHPUnit_Framework_MockObject_MockObject */
        $driver = $this->getMock('Integrated\Bundle\SolrBundle\Mapping\Driver\Metadata\DriverInterface');
        $this->driver->addDriver($driver, 'Test');

        /* @var $class \ReflectionClass | \PHPUnit_Framework_MockObject_MockObject */
        $class = $this->getMock('ReflectionClass', array(), array(), '', false);

        $metadata = $this->getMockClass('Integrated\Bundle\SolrBundle\Mapping\Metadata\Metadata');

        // Stub loadMetadataForClass
        $driver->expects($this->once())
            ->method('loadMetadataForClass')
            ->will($this->returnValue($metadata));

        // Assert
        $this->assertSame($metadata, $this->driver->loadMetadataForClass($class));
    }

    /**
     * Test the loadMetadataForClass function with zero drivers
     */
    public function testLoadMetadataForClassFunctionWithZeroDrivers()
    {
        /* @var $class \ReflectionClass | \PHPUnit_Framework_MockObject_MockObject */
        $class = $this->getMock('ReflectionClass', array(), array(), '', false);

        $this->assertNull($this->driver->loadMetadataForClass($class));
    }
}