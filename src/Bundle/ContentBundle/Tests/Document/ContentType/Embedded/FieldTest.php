<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\Document\ContentType\Embedded;

use Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\Field;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class FieldTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Field
     */
    private $field;

    /**
     * Setup the test.
     */
    protected function setUp(): void
    {
        $this->field = new Field();
    }

    /**
     * Test instance of.
     */
    public function testInstanceOf()
    {
        $this->assertInstanceOf('Integrated\Common\ContentType\ContentTypeFieldInterface', $this->field);
    }

    /**
     * Test name property.
     */
    public function testName()
    {
        $name = 'name';
        $this->assertEquals($name, $this->field->setName($name)->getName());
    }

    /**
     * Test options property.
     */
    public function testOptions()
    {
        $options = ['label' => 'label', 'required' => false];
        $this->assertEquals($options, $this->field->setOptions($options)->getOptions());
    }

    /**
     * Test getLabel function.
     */
    public function testGetLabelFunction()
    {
        $name = 'name';
        $this->field->setName($name);

        $this->assertSame(ucfirst($name), $this->field->getLabel());

        $label = 'label';
        $this->field->setOptions(['label' => $label]);
        $this->assertSame($label, $this->field->getLabel());
    }
}
