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

use Integrated\Bundle\ContentBundle\Document\Content\File;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class FileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var File
     */
    private $file;

    /**
     * Setup the test
     */
    protected function setUp()
    {
        $this->file = new File();
    }

    /**
     * File should implement ContentInterface
     */
    public function testContentInterface()
    {
        $this->assertInstanceOf('Integrated\Common\Content\ContentInterface', $this->file);
    }

    /**
     * File should extend AbstractContent
     */
    public function testAbstractContent()
    {
        $this->assertInstanceOf('Integrated\Bundle\ContentBundle\Document\Content\AbstractContent', $this->file);
    }

    /**
     * Test get- and setTitle function
     */
    public function testGetAndSetTitleFunction()
    {
        $title = array('nl' => 'Titel', 'en' => 'Title');
        $this->assertSame($title, $this->file->setTitle($title)->getTitle());
    }

    /**
     * Test get- and setFile function
     */
    public function testGetAndSetFileFunction()
    {
        $file = 'file';
        $this->assertEquals($file, $this->file->setFile($file)->getFile());
    }

    /**
     * Test get- and setDescription function
     */
    public function testGetAndSetTypeFunction()
    {
        $description = array('nl' => 'Omschrijving', 'en' => 'Description');
        $this->assertEquals($description, $this->file->setDescription($description)->getDescription());
    }
}