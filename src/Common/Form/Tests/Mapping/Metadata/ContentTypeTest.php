<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Form\Tests\Mapping\Metadata;

use Integrated\Common\Form\Mapping\Metadata\Document;
use Integrated\Common\Form\Mapping\Metadata\Field;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ContentTypeTest extends \PHPUnit\Framework\TestCase
{
    const CONTENT_TYPE_CLASS = 'class';

    /**
     * @var Document
     */
    private $contentType;

    /**
     * Setup the test.
     */
    protected function setUp(): void
    {
        $this->contentType = new Document(self::CONTENT_TYPE_CLASS);
    }

    /**
     * Test getter and setter of class.
     */
    public function testClass()
    {
        $this->assertEquals(self::CONTENT_TYPE_CLASS, $this->contentType->getClass());
    }

    /**
     * Test getters and setter of type.
     */
    public function testType()
    {
        $this->contentType->setType('Henk');
        $this->assertEquals('Henk', $this->contentType->getType());
    }

    /**
     * Test getter and setter of fields.
     */
    public function testFields()
    {
        /** @var Field | \PHPUnit_Framework_MockObject_MockObject $field1 */
        $field1 = $this->getMockBuilder(Field::class)->disableOriginalConstructor()->getMock();

        $field1
            ->expects($this->once())
            ->method('getName')
            ->willReturn('field1')
        ;

        /** @var Field | \PHPUnit_Framework_MockObject_MockObject $field2 */
        $field2 = $this->getMockBuilder(Field::class)->disableOriginalConstructor()->getMock();

        // Set fields
        $fields = [
            'field1' => $field1,
        ];

        $this->contentType->addField($field1);

        // Assert
        $this->assertSame($fields, $this->contentType->getFields());

        // Stub getName
        $field2
            ->expects($this->once())
            ->method('getName')
            ->willReturn('field2')
        ;

        // Add field
        $this->contentType->addField($field2);

        // Assert
        $this->assertSame($field2, $this->contentType->getField('field2'));
        $this->assertNull($this->contentType->getField('field3'));
    }
}
