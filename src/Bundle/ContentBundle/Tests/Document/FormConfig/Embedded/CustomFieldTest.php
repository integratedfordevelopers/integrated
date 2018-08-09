<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\Document\FormConfig\Embedded;

use Integrated\Bundle\ContentBundle\Document\FormConfig\Embedded\CustomField;

class CustomFieldTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CustomField
     */
    private $field;

    /**
     * Setup the test.
     */
    protected function setUp()
    {
        $this->field = new CustomField();
    }

    /**
     * Test instance of.
     */
    public function testInstanceOf()
    {
        $this->assertInstanceOf('Integrated\Common\FormConfig\FormConfigFieldInterface', $this->field);
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
     * Test type property.
     */
    public function testType()
    {
        $type = 'type';
        $this->assertEquals($type, $this->field->setType($type)->getType());
    }

    /**
     * Test options property.
     */
    public function testOptions()
    {
        $options = ['label' => 'label', 'required' => false];
        $this->assertEquals($options, $this->field->setOptions($options)->getOptions());
    }
}
