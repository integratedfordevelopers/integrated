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
use Integrated\Common\Form\Mapping\Driver\AnnotationDriver;

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
     * @var AnnotationDriver
     */
    private $driver;

    /**
     * Setup the test.
     */
    protected function setUp()
    {
        // Mock Reader
        $this->reader = $this->createMock('Doctrine\Common\Annotations\Reader');

        // Create Driver
        //$this->driver = new AnnotationDriver($this->reader);
    }

    /**
     * Test the AnnotationsDriver LoadMetadataForClass function.
     */
    public function testLoadMetadataForClassFunction()
    {
        $this->markTestSkipped('Driver is refactored');

        // TODO: mock reflection class
        // Create reflection class
        $class = new \ReflectionClass(new Test());

        // TODO: mock document class
        // Create annotations Document
        $document = new Document(['name' => 'Henk de Vries']);

        // Stub getClassAnnotation function
        $this->reader->expects($this->once())
            ->method('getClassAnnotation')
            ->will($this->returnValue($document));

        // TODO: mock field class
        // Create annotations Field
        $field = new Field(['options' => ['label' => 'Do you love Henk?']]);

        // Stub getPropertyAnnotation function
        $this->reader->expects($this->exactly(2))
            ->method('getPropertyAnnotation')
            ->will($this->returnValue($field));

        // Assert
        $this->assertInstanceOf('Integrated\Common\Form\Mapping\Metadata\Document', $this->driver->loadMetadataForClass($class));
    }

    /**
     * Test the AnnotationsDriver LoadMetadataForClass function to return null.
     */
    public function testLoadMetadataForClassFunctionReturnNull()
    {
        $this->markTestSkipped('Driver is refactored');

        // TODO: mock reflection class
        // Create reflection class
        $class = new \ReflectionClass(new \stdClass());

        // Assert
        $this->assertNull($this->driver->loadMetadataForClass($class));
    }
}
