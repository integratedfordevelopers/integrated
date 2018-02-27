<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Form\Tests\Mapping\Annotations;

use Integrated\Common\Form\Mapping\Annotations\Field;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class FieldTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test the constructor with valid data.
     */
    public function testConstructorWithValidData()
    {
        // Create options array
        $options = ['label' => 'de Vries', 'required' => true];

        // Create field
        $field = new Field(['type' => 'Henk', 'options' => $options]);

        // Asserts
        $this->assertEquals('Henk', $field->getType());
        $this->assertSame($options, $field->getOptions());
    }

    /**
     * Test the constructor with invalid data.
     */
    public function testConstructorWithInvalidData()
    {
        $this->expectException(\BadMethodCallException::class);

        new Field(['Henk' => 'type']);
    }

    /**
     * Test the constructor with default values.
     */
    public function testConstructorWithDefaultValues()
    {
        // Create field
        $field = new Field([]);

        // Asserts
        $this->assertEquals(TextType::class, $field->getType());
        $this->assertSame([], $field->getOptions());
    }
}
