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

use Integrated\Common\Form\Mapping\Metadata\Field;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ContentTypeFieldTest extends \PHPUnit\Framework\TestCase
{
    const NAME = 'name';

    /**
     * @var Field
     */
    private $contentTypeField;

    /**
     * Setup the test.
     */
    protected function setUp(): void
    {
        $this->contentTypeField = new Field(self::NAME);
    }

    /**
     * Test getter and setter of name.
     */
    public function testName()
    {
        $this->assertEquals(self::NAME, $this->contentTypeField->getName());
    }

    /**
     * Test getters and setter of type.
     */
    public function testType()
    {
        $type = 'Henk';
        $this->contentTypeField->setType($type);
        $this->assertEquals($type, $this->contentTypeField->getType());
    }

    /**
     * Test getters and setter of options.
     */
    public function testOptions()
    {
        $options = ['label' => 'Label', 'required' => false];
        $this->contentTypeField->setOptions($options);
        $this->assertEquals($options, $this->contentTypeField->getOptions());
    }
}
