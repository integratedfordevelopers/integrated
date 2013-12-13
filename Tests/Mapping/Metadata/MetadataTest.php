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

use Integrated\Bundle\SolrBundle\Mapping\Metadata\Metadata;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class MetadataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Metadata
     */
    protected $metadata;

    /**
     * Setup the test
     */
    protected function setUp()
    {
        $this->metadata = new Metadata('test');
    }

    /**
     * Test the constructor
     */
    public function testConstructor()
    {
        $this->assertEquals('test', $this->metadata->getClass());
    }

    /**
     * Test the default values
     */
    public function testDefaultValues()
    {
        $this->assertFalse($this->metadata->getIndex());
        $this->assertSame(array(), $this->metadata->getFields());
    }

    /**
     * Test get- and setClass function
     */
    public function testGetAndSetClassFunction()
    {
        $class = 'class';
        $this->assertSame($this->metadata, $this->metadata->setClass($class));
        $this->assertSame($class, $this->metadata->getClass());
    }

    /**
     * Test get- and setIndex function
     */
    public function testGetAndSetIndexFunction()
    {
        $this->assertSame($this->metadata, $this->metadata->setIndex(true));
        $this->assertTrue($this->metadata->getIndex());
    }

    /**
     * Test get- and setFields function
     */
    public function testGetAndSetFieldsFunction()
    {
        $fields = array(1, 2);
        $this->assertSame($this->metadata, $this->metadata->setFields($fields));
        $this->assertSame($fields, $this->metadata->getFields());
    }
}