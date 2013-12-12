<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SolrBundle\Mapping\Driver\Metadata;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class AnnotationsDriverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Doctrine\Common\Annotations\Reader | \PHPUnit_Framework_MockObject_MockObject
     */
    private $reader;

    /**
     * @var AnnotationsDriver
     */
    private $driver;

    /**
     * Setup the test
     */
    protected function setUp()
    {
        // Mock Reader
        $this->reader = $this->getMock('Doctrine\Common\Annotations\Reader');

        // Create Driver
        $this->driver = new AnnotationsDriver($this->reader);
    }

    /**
     * Driver should implement DriverInterface
     */
    public function testInstanceofDriverInterface()
    {
        $this->assertInstanceOf('Integrated\Bundle\SolrBundle\Mapping\Driver\Metadata\DriverInterface', $this->driver);
    }

    /**
     * Test loadMetadataForClass function with two properties
     */
    public function testLoadMetadataForClassFunctionWithTwoProperties()
    {
        /* @var $class \ReflectionClass | \PHPUnit_Framework_MockObject_MockObject */
        $class = $this->getMock('ReflectionClass', array(), array(), '', false);

        /* @var $property1 \ReflectionProperty | \PHPUnit_Framework_MockObject_MockObject */
        $property1 = $this->getMock('ReflectionProperty', array(), array(), '', false);

        /* @var $property2 \ReflectionProperty | \PHPUnit_Framework_MockObject_MockObject */
        $property2 = $this->getMock('ReflectionProperty', array(), array(), '', false);

        // Stub getProperties function
        $class->expects($this->once())
            ->method('getProperties')
            ->will($this->returnValue(array($property1, $property2)));

        /* @var $class \Integrated\Bundle\SolrBundle\Mapping\Annotations\Document | \PHPUnit_Framework_MockObject_MockObject */
        $document = $this->getMock('Integrated\Bundle\SolrBundle\Mapping\Annotations\Document', array(), array(), '', false);

        // Stub getClassAnnotation function
        $this->reader->expects($this->once())
            ->method('getClassAnnotation')
            ->will($this->returnValue($document));

        /* @var $field \Integrated\Bundle\SolrBundle\Mapping\Annotations\Field | \PHPUnit_Framework_MockObject_MockObject */
        $field = $this->getMock('Integrated\Bundle\SolrBundle\Mapping\Annotations\Field', array(), array(), '', false);

        // Stub getPropertyAnnotation function
        $this->reader->expects($this->exactly(2))
            ->method('getPropertyAnnotation')
            ->will($this->returnValue($field));

        // Assert
        $this->assertInstanceOf('Integrated\Bundle\SolrBundle\Mapping\Metadata\Metadata', $this->driver->loadMetadataForClass($class));
    }

    /**
     * Test the AnnotationsDriver LoadMetadataForClass function to return null
     */
    public function testLoadMetadataForClassFunctionReturnNull()
    {
        /* @var $class \ReflectionClass | \PHPUnit_Framework_MockObject_MockObject */
        $class = $this->getMock('ReflectionClass', array(), array(), '', false);

        // Assert
        $this->assertNull($this->driver->loadMetadataForClass($class));
    }
}