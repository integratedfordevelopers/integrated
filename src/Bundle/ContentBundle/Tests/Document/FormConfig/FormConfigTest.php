<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\Document\FormConfig;

use Integrated\Bundle\ContentBundle\Document\FormConfig\FormConfig;
use Integrated\Common\FormConfig\FormConfigFieldInterface;
use Integrated\Common\FormConfig\FormConfigInterface;

class FormConfigTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var FormConfig
     */
    private $formConfig;

    /**
     * Setup the test.
     */
    protected function setUp()
    {
        $this->formConfig = new FormConfig();
    }

    /**
     * FormConfig should implement FormConfigInterface.
     */
    public function testInstanceOfFormConfigInterface()
    {
        $this->assertInstanceOf(FormConfigInterface::class, $this->formConfig);
    }

    /**
     * Test get- and setId function.
     */
    public function testGetAndSetIdFunction()
    {
        $id = 'abc123';
        $this->assertEquals($id, $this->formConfig->setId($id)->getId());
    }

    /**
     * Test get- and setName function.
     */
    public function testGetAndSetNameFunction()
    {
        $name = 'Henk de Vries';
        $this->assertEquals($name, $this->formConfig->setName($name)->getName());
    }

    /**
     * Test get- and setFields function.
     */
    public function testGetAndSetFieldsFunction()
    {
        // Mock fields
        $field1 = $this->getMockClass(FormConfigFieldInterface::class);
        $field2 = $this->getMockClass(FormConfigFieldInterface::class);

        $fields = [
            $field1,
            $field2,
        ];

        // Assert
        $this->assertSame($fields, $this->formConfig->setFields($fields)->getFields());
    }

    /**
     * Test getField function.
     */
    public function testGetFieldFunction()
    {
        // Mock fields
        $field = $this->createMock(FormConfigFieldInterface::class);
        $field->expects($this->exactly(2))
            ->method('getName')
            ->will($this->returnValue('henk'));

        $this->formConfig->setFields([$field]);

        // Asserts
        $this->assertSame($field, $this->formConfig->getField('henk'));
        $this->assertNull($this->formConfig->getField('henkie'));
    }

    /**
     * Test hasField function.
     */
    public function testHasFieldFunction()
    {
        // Mock fields
        $field = $this->createMock(FormConfigFieldInterface::class);
        $field->expects($this->exactly(2))
            ->method('getName')
            ->will($this->returnValue('henk'));

        $this->formConfig->setFields([$field]);

        // Asserts
        $this->assertTrue($this->formConfig->hasField('henk'));
        $this->assertFalse($this->formConfig->hasField('henkie'));
    }
}
