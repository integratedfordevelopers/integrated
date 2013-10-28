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

use Integrated\Common\ContentType\Mapping\Metadata\ContentType;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ContentTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContentType
     */
    private $contentType;

    /**
     * Setup the test
     */
    protected function setUp()
    {
        $this->contentType = new ContentType();
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
        // Mock ContentTypeField
        $field1 = $this->getMock('Integrated\Common\ContentType\Mapping\Metadata\ContentTypeField');
        $field2 = $this->getMock('Integrated\Common\ContentType\Mapping\Metadata\ContentTypeField');

        // Set fields
        $fields = array(
            $field1
        );

        $this->contentType->setFields(array($field1));

        // Assert
        $this->assertSame($fields, $this->contentType->getFields());

        // Stub getName
        $field2->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('Henk'));

        // Add field
        $this->contentType->addField($field2);

        // Assert
        $this->assertSame($field2, $this->contentType->getField('Henk'));
    }
}