<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\ContentType\Mapping\Annotations;

use Integrated\Common\ContentType\Mapping\Annotations\Field;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class FieldTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the constructor with valid data
     */
    public function testConstructorWithValidData()
    {
        // Create field
        $field = new Field(array('type' => 'Henk', 'label' => 'de Vries', 'required' => true));

        // Asserts
        $this->assertEquals('Henk', $field->getType());
        $this->assertEquals('de Vries', $field->getLabel());
        $this->assertTrue($field->getRequired());
    }


    /**
     * Test the constructor with invalid data
     *
     * @expectedException \BadMethodCallException
     */
    public function testConstructorWithInvalidData()
    {
        new Field(array('Henk' => 'type'));
    }

    /**
     * Test the constructor with default values
     */
    public function testConstructorWithDefaultValues()
    {
        // Create field
        $field = new Field(array());

        // Asserts
        $this->assertEquals('text', $field->getType());
        $this->assertFalse($field->getRequired());
    }
}