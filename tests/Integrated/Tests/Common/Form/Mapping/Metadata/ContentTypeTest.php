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

use Integrated\Common\Form\Mapping\Metadata\Document;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ContentTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Document
     */
    private $contentType;

    /**
     * Setup the test
     */
    protected function setUp()
    {
        $this->contentType = new Document('');
    }

    /**
     * Test getter and setter of class
     */
    public function testClass()
    {
        $this->contentType->setClass('Henk');
        $this->assertEquals('Henk', $this->contentType->getClass());
    }

    /**
     * Test getters and setter of type
     */
    public function testType()
    {
        $this->contentType->setType('Henk');
        $this->assertEquals('Henk', $this->contentType->getType());
    }

    /**
     * Test getter and setter of fields
     */
    public function testFields()
    {
        // Mock Field
        $field1 = $this->getMock('Integrated\Common\Form\Mapping\Metadata\Field');
        $field2 = $this->getMock('Integrated\Common\Form\Mapping\Metadata\Field');

        // Set fields
        $fields = array(
            $field1
        );

        $this->contentType->setFields(array($field1));

        // Assert
        $this->assertSame($fields, $this->contentType->getFields());

        // Stub getName
        $field2->expects($this->exactly(2))
            ->method('getName')
            ->will($this->returnValue('Henk'));

        // Add field
        $this->contentType->addField($field2);

        // Assert
        $this->assertSame($field2, $this->contentType->getField('Henk'));
        $this->assertNull($this->contentType->getField('Henk de Vries'));
    }
}