<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\Form\Mapping\Driver;

use Integrated\Common\Form\Mapping\Metadata\ContentTypeAttribute;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ContentTypeFieldTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContentTypeAttribute
     */
    private $contentTypeField;

    /**
     * Setup the test
     */
    protected function setUp()
    {
        $this->contentTypeField = new ContentTypeAttribute();
    }

    /**
     * Test getter and setter of name
     */
    public function testName()
    {
        $name = 'Henk';
        $this->contentTypeField->setName($name);
        $this->assertEquals($name, $this->contentTypeField->getName());
    }

    /**
     * Test getters and setter of type
     */
    public function testType()
    {
        $type = 'Henk';
        $this->contentTypeField->setType($type);
        $this->assertEquals($type, $this->contentTypeField->getType());
    }

    /**
     * Test getters and setter of options
     */
    public function testOptions()
    {
        $options = array('label' => 'Label', 'required' => false);
        $this->contentTypeField->setOptions($options);
        $this->assertEquals($options, $this->contentTypeField->getOptions());
    }

    /**
     * Test getLabel function
     */
    public function testGetLabelFunction()
    {
        $name = 'name';
        $this->contentTypeField->setName($name);

        $this->assertEquals(ucfirst($name), $this->contentTypeField->getLabel());

        $options = array('label' => 'Henk de Vries');
        $this->contentTypeField->setOptions($options);

        $this->assertEquals($options['label'], $this->contentTypeField->getLabel());
    }
}