<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\Form\Mapping\Driver;

use Doctrine\Common\Annotations\Reader;
use Integrated\Common\Form\Mapping\Annotations\Document;
use Integrated\Common\Form\Mapping\Annotations\Field;
use Integrated\Common\Form\Mapping\Driver\AnnotationsDriver;

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
        // TODO: mock reflection class
        // Create reflection class
        $class = new \ReflectionClass(new Test());

        // TODO: mock document class
        // Create annotations Document
        $document = new Document(array('name' => 'Henk de Vries'));

        // Stub getClassAnnotation function
        $this->reader->expects($this->once())
            ->method('getClassAnnotation')
            ->will($this->returnValue($document));

        // TODO: mock field class
        // Create annotations Field
        $field = new Field(array('options' => array('label' => 'Do you love Henk?')));

        // Stub getPropertyAnnotation function
        $this->reader->expects($this->exactly(2))
            ->method('getPropertyAnnotation')
            ->will($this->returnValue($field));

        // Assert
        $this->assertInstanceOf('Integrated\Common\Form\Mapping\Metadata\ContentType', $this->driver->loadMetadataForClass($class));
    }

    /**
     * Test the AnnotationsDriver LoadMetadataForClass function to return null
     */
    public function testLoadMetadataForClassFunctionReturnNull()
    {
        // TODO: mock reflection class
        // Create reflection class
        $class = new \ReflectionClass(new Test());

        // Assert
        $this->assertNull($this->driver->loadMetadataForClass($class));
    }
}


/**
 * Dummy class with two properties
 * @todo reflection class should be mocked
 */
class Test
{
    protected $property1;
    protected $property2;
}