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

use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Storage;
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
    public function testInstanceOfContentInterface()
    {
        $this->assertInstanceOf('Integrated\Common\Content\ContentInterface', $this->file);
    }

    /**
     * File should extend Content
     */
    public function testInstanceOfContent()
    {
        $this->assertInstanceOf('Integrated\Bundle\ContentBundle\Document\Content\Content', $this->file);
    }

    /**
     * Test get- and setTitle function
     */
    public function testGetAndSetTitleFunction()
    {
        $title = 'title';
        $this->assertSame($title, $this->file->setTitle($title)->getTitle());
    }

    /**
     * Test get- and setFile function
     */
    public function testGetAndSetFileFunction()
    {
        $this->assertEquals(
            $file = $this->getMock('Integrated\Common\Content\Document\Storage\Embedded\StorageInterface'),
            $this->file->setFile($file)->getFile()
        );
    }

    /**
     * Test get- and setDescription function
     */
    public function testGetAndSetDescriptionFunction()
    {
        $description = 'description';
        $this->assertEquals($description, $this->file->setDescription($description)->getDescription());
    }

    /**
     * Test toString function
     */
    public function testToStringFunction()
    {
        $title = 'Title';
        $this->assertEquals($title, (string) $this->file->setTitle($title));
    }
}
