<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SolrBundle\Tests\Mapping\Metadata;

use Integrated\Bundle\SolrBundle\Mapping\Metadata\MetadataFactory;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class MetadataFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MetadataFactory
     */
    protected $metadataFactory;

    /**
     * @var \Integrated\Bundle\SolrBundle\Mapping\Driver\Metadata\DriverInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $driver;

    /**
     * Setup the test
     */
    protected function setUp()
    {
        $this->driver= $this->getMock('Integrated\Bundle\SolrBundle\Mapping\Driver\Metadata\DriverInterface');
        $this->metadataFactory = new MetadataFactory($this->driver);
    }

    /**
     * Test build function once
     */
    public function testBuildFunctionOnce()
    {
        // Get mock class
        $class = $this->getMockClass('Test');

        // Stub loadMetadataForClass function
        $return = '123';
        $this->driver->expects($this->once())
            ->method('loadMetadataForClass')
            ->will($this->returnValue($return));

        // Assert
        $this->assertSame($return, $this->metadataFactory->build($class));
    }

    /**
     * Test build function twice
     */
    public function testBuildFunctionTwice()
    {
        // Get mock class
        $class = $this->getMockClass('Test');

        // Stub loadMetadataForClass function
        $return = '123';
        $this->driver->expects($this->once())
            ->method('loadMetadataForClass')
            ->will($this->returnValue($return));

        // Assert
        $this->assertSame($return, $this->metadataFactory->build($class));
        $this->assertSame($return, $this->metadataFactory->build($class));
    }
}