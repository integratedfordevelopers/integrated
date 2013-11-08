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

use Integrated\MongoDB\ContentType\Document\Embedded\Field;

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
     * Test name property
     */
    public function testName()
    {
        $name = 'henk';
        $this->assertEquals($name, $this->field->setName($name)->getName());
    }

    /**
     * Test type property
     */
    public function testType()
    {
        $type = 'henk';
        $this->assertEquals($type, $this->field->setType($type)->getType());
    }

    /**
     * Test options property
     */
    public function testOptions()
    {
        $options = array('label' => 'Test', 'required' => false);
        $this->assertEquals($options, $this->field->setOptions($options)->getOptions());
    }
}