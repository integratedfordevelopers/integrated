<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SolrBundle\Mapping\Annotations;

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
        // Create options array
        $options = array('index' => true, 'display' => true, 'facet' => true);

        // Create field
        $field = new Field($options);

        // Asserts
        $this->assertTrue($field->getIndex());
        $this->assertTrue($field->getDisplay());
        $this->assertTrue($field->getFacet());
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

        // Assert
        $this->assertFalse($field->getIndex());
        $this->assertFalse($field->getFacet());
        $this->assertFalse($field->getSort());
        $this->assertFalse($field->getDisplay());
    }

    /**
     * Test get- and setIndex function
     */
    public function testGetAndSetIndexFunction()
    {
        $field = new Field(array());
        $field->setIndex(true);
        $this->assertTrue($field->getIndex());
    }

    /**
     * Test get- and setFacet function
     */
    public function testGetAndSetFacetFunction()
    {
        $field = new Field(array());
        $field->setFacet(true);
        $this->assertTrue($field->getFacet());
    }

    /**
     * Test get- and setSort function
     */
    public function testGetAndSetSortFunction()
    {
        $field = new Field(array());
        $field->setSort(true);
        $this->assertTrue($field->getSort());
    }

    /**
     * Test get- and setDisplay function
     */
    public function testGetAndSetDisplayFunction()
    {
        $field = new Field(array());
        $field->setDisplay(true);
        $this->assertTrue($field->getDisplay());
    }
}