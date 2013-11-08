<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\MongoDB\ContentType\Document;

use Integrated\MongoDB\ContentType\Document\ContentType;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ContentTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContentType
     */
    private $contentType;

    protected function setUp()
    {
        $this->contentType = new ContentType();
    }

    /**
     * Test the create functions
     */
    public function testCreate()
    {
        // Mock ContentInterface
        $content = $this->getMock('Integrated\Common\Content\ContentInterface');

        // Set class
        $class = get_class($content);
        $this->contentType->setClass($class);

        // Assert
        $this->assertInstanceOf($class, $this->contentType->create());
    }

    /**
     * Test id property
     */
    public function testId()
    {
        $id = 'abc123';
        $this->assertEquals($id, $this->contentType->setId($id)->getId());
    }

    /**
     * Test class property
     */
    public function testClass()
    {
        $class = 'HenkDeVries';
        $this->assertEquals($class, $this->contentType->setClass($class)->getClass());
    }

    /**
     * Test type property
     */
    public function testType()
    {
        $type = 'Familie De Vries';
        $this->assertEquals($type, $this->contentType->setType($type)->getType());
    }

    /**
     * Test fields property
     */
    public function testFields()
    {
        // Mock fields
        $field1 = $this->getMockClass('Integrated\Common\ContentType\ContentTypeFieldInterface');
        $field2 = $this->getMockClass('Integrated\Common\ContentType\ContentTypeFieldInterface');

        $fields = array(
            $field1,
            $field2
        );

        // Assert
        $this->assertSame($fields, $this->contentType->setFields($fields)->getFields());
    }

    /**
     * Test getField function
     */
    public function testGetFieldFunction()
    {
        // Mock fields
        $field = $this->getMock('Integrated\Common\ContentType\ContentTypeFieldInterface');
        $field->expects($this->exactly(2))
            ->method('getName')
            ->will($this->returnValue('henk'));

        $this->contentType->setFields(array($field));

        // Asserts
        $this->assertSame($field, $this->contentType->getField('henk'));
        $this->assertNull($this->contentType->getField('henkie'));
    }

    /**
     * Test hasField function
     */
    public function testHasFieldFunction()
    {
        // Mock fields
        $field = $this->getMock('Integrated\Common\ContentType\ContentTypeFieldInterface');
        $field->expects($this->exactly(2))
            ->method('getName')
            ->will($this->returnValue('henk'));

        $this->contentType->setFields(array($field));

        // Asserts
        $this->assertTrue($this->contentType->hasField('henk'));
        $this->assertFalse($this->contentType->hasField('henkie'));
    }

    /**
     * Test relations property
     */
    public function testRelations()
    {
        // Mock fields
        $relation1 = $this->getMockClass('Integrated\Common\ContentType\ContentTypeRelationInterface');
        $relation2 = $this->getMockClass('Integrated\Common\ContentType\ContentTypeRelationInterface');

        $relations = array(
            $relation1,
            $relation2
        );

        // Assert
        $this->assertSame($relations, $this->contentType->setRelations($relations)->getRelations());
    }

    // TODO test getRelation and hasRelation


}