<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\Document\FormConfig\Embedded\Field;

use Integrated\Bundle\ContentBundle\Document\FormConfig\Embedded\Field\DocumentField;
use Integrated\Common\FormConfig\FormConfigFieldInterface;

class DocumentFieldTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var DocumentField
     */
    private $field;

    /**
     * Setup the test.
     */
    protected function setUp()
    {
        $this->field = new DocumentField('name', 'type', ['option1' => 'value1', 'option2' => 'value2']);
    }

    public function testIterface()
    {
        $this->assertInstanceOf(FormConfigFieldInterface::class, $this->field);
    }

    public function testName()
    {
        $this->assertEquals('name', $this->field->getName());
    }

    public function testType()
    {
        $this->assertEquals('type', $this->field->getType());
    }

    public function testOptions()
    {
        $this->assertEquals(['option1' => 'value1', 'option2' => 'value2'], $this->field->getOptions());
    }
}
