<?php

namespace Integrated\Bundle\StorageBundle\Tests\Document;

use Integrated\Bundle\StorageBundle\Document\File;
use Integrated\Bundle\StorageBundle\Document\Image;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class ImageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests whether the Image class is an instanceof File
     */
    public function testInstanceOf()
    {
        $image = new Image();

        $this->assertTrue($image instanceof File);
    }
}
