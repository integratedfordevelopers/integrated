<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Tests\Document;

use Integrated\Bundle\StorageBundle\Document\File;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class FileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the get- and setTitle method
     */
    public function testGetAndSetTitle()
    {
        $file = new File();
        $file->setTitle($title = 'title');

        $this->assertEquals($title, $file->getTitle());
    }

    /**
     * Test the get- and setDescription method
     */
    public function testGetAndSetDescription()
    {
        $file = new File();
        $file->setDescription($description = 'description');

        $this->assertEquals($description, $file->getDescription());
    }

    /**
     * Test the get- and setFile method
     */
    public function testGetAndSetFileMethod()
    {
        $file = new File();
        $file->setFile(
        // This object will be asserted, keep it in track
            $storage =
                // Create a mock
                $this->getMockBuilder('Integrated\Bundle\StorageBundle\Document\Embedded\Storage')
                    ->disableOriginalConstructor()
                    ->getMock()
        );

        $this->assertEquals($storage, $file->getFile());
    }

    /**
     * Test the toString method of the class
     */
    public function testToStringMethod()
    {
        $file = new File();
        $file->setTitle($toString = 'toString');

        $this->assertEquals($toString, (string) $file);
    }
}
