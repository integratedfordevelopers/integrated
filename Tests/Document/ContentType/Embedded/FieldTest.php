<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\MongoDB\ContentType\Document\Embedded;

use Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\Field;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class FieldTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Field
     */
    private $field;

    /**
     * Setup the test
     */
    protected function setUp()
    {
        $this->field = new Field();
    }

    /**
     * Test instance of
     */
    public function testInstanceOf()
    {
        $this->assertInstanceOf('Integrated\Common\ContentType\ContentTypeFieldInterface', $this->field);
    }

    /**
     * Test name property
     */
    public function testName()
    {
        $name = 'name';
        $this->assertEquals($name, $this->field->setName($name)->getName());
    }

    /**
     * Test type property
     */
    public function testType()
    {
        $type = 'type';
        $this->assertEquals($type, $this->field->setType($type)->getType());
    }

    /**
     * Test options property
     */
    public function testOptions()
    {
        $options = array('label' => 'label', 'required' => false);
        $this->assertEquals($options, $this->field->setOptions($options)->getOptions());
    }

    /**
     * Test getLabel function
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
