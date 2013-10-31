<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\ContentType\Mapping\Driver;

use Doctrine\Common\Annotations\Reader;
use Integrated\Common\ContentType\Mapping\Annotations\Document;
use Integrated\Common\ContentType\Mapping\Annotations\Field;
use Integrated\Common\ContentType\Mapping\Driver\AnnotationsDriver;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class AnnotationsDriverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Reader | \PHPUnit_Framework_MockObject_MockObject
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
     * Test the AnnotationsDriver LoadMetadataForClass function
     */
    public function testLoadMetadataForClassFunction()
    {
        // Create reflection class
        $class = new \ReflectionClass(new Test());

        // Create annotations Document
        $document = new Document(array('name' => 'Henk de Vries'));

        // Stub getClassAnnotation function
        $this->reader->expects($this->once())
            ->method('getClassAnnotation')
            ->will($this->returnValue($document));

        // Create annotations Field
        $field = new Field(array('label' => 'Do you love Henk?'));

        // Stub getPropertyAnnotation function
        $this->reader->expects($this->exactly(2))
            ->method('getPropertyAnnotation')
            ->will($this->returnValue($field));

        // Assert
        $this->assertInstanceOf('Integrated\Common\ContentType\Mapping\Metadata\ContentType', $this->driver->loadMetadataForClass($class));
    }

    /**
     * Test the AnnotationsDriver LoadMetadataForClass function to return null
     */
    public function testLoadMetadataForClassFunctionReturnNull()
    {
        // Create reflection class
        $class = new \ReflectionClass(new Test());

        // Assert
        $this->assertNull($this->driver->loadMetadataForClass($class));
    }
}

/**
 * Dummy class with two properties
 */
class Test
{
    protected $property1;
    protected $property2;
}