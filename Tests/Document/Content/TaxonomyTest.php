<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\Document\Content;

use Integrated\Bundle\ContentBundle\Document\Content\Taxonomy;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class TaxonomyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Taxonomy
     */
    private $taxonomy;

    /**
     * Setup the test
     */
    protected function setUp()
    {
        $this->taxonomy = new Taxonomy();
    }

    /**
     * Taxonomy should implement ContentInterface
     */
    public function testInstanceOfContentInterface()
    {
        $this->assertInstanceOf('Integrated\Common\Content\ContentInterface', $this->taxonomy);
    }

    /**
     * Taxonomy should extend Content
     */
    public function testInstanceOfContent()
    {
        $this->assertInstanceOf('Integrated\Bundle\ContentBundle\Document\Content\Content', $this->taxonomy);
    }

    /**
     * Test get- and setTitle function
     */
    public function testGetAndSetTitleFunction()
    {
        $title = 'title';
        $this->assertSame($title, $this->taxonomy->setTitle($title)->getTitle());
    }

    /**
     * Test get- and setDescription function
     */
    public function testGetAndSetDescriptionFunction()
    {
        $description = 'description';
        $this->assertEquals($description, $this->taxonomy->setDescription($description)->getDescription());
    }

    /**
     * Test toString function
     */
    public function testToStringFunction()
    {
        $title = 'Title';
        $this->assertEquals($title, (string) $this->taxonomy->setTitle($title));
    }
}