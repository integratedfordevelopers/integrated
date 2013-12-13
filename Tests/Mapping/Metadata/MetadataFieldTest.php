<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SolrBundle\Tests\Mapping\Metadata;

use Integrated\Bundle\SolrBundle\Mapping\Metadata\MetadataField;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class MetadataFieldTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MetadataField
     */
    protected $metadataField;

    /**
     * Setup the test
     */
    protected function setUp()
    {
        $this->metadataField = new MetadataField('test');
    }

    /**
     * Test the constructor
     */
    public function testConstructor()
    {
        $this->assertEquals('test', $this->metadataField->getName());
    }

    /**
     * Test the default values
     */
    public function testDefaultValues()
    {
        $this->assertFalse($this->metadataField->getIndex());
        $this->assertFalse($this->metadataField->getFacet());
        $this->assertFalse($this->metadataField->getSort());
        $this->assertFalse($this->metadataField->getDisplay());
    }

    /**
     * Test get- and setName function
     */
    public function testGetAndSetNameFunction()
    {
        $name = 'name';
        $this->assertSame($this->metadataField, $this->metadataField->setName($name));
        $this->assertSame($name, $this->metadataField->getName());
    }

    /**
     * Test get- and setIndex function
     */
    public function testGetAndSetIndexFunction()
    {
        $this->assertSame($this->metadataField, $this->metadataField->setIndex(true));
        $this->assertTrue($this->metadataField->getIndex());
    }

    /**
     * Test get- and setFacet function
     */
    public function testGetAndSetFacetFunction()
    {
        $this->assertSame($this->metadataField, $this->metadataField->setFacet(true));
        $this->assertTrue($this->metadataField->getFacet());
    }

    /**
     * Test get- and setSort function
     */
    public function testGetAndSetSortFunction()
    {
        $this->assertSame($this->metadataField, $this->metadataField->setSort(true));
        $this->assertTrue($this->metadataField->getSort());
    }

    /**
     * Test get- and setIndex function
     */
    public function testGetAndSetDisplayFunction()
    {
        $this->assertSame($this->metadataField, $this->metadataField->setDisplay(true));
        $this->assertTrue($this->metadataField->getDisplay());
    }
}