<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\Document\Content\Embedded\Storage;

use Doctrine\Common\Collections\ArrayCollection;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Storage\Metadata;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class MetadataTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests the getters (through the constructor) of the object.
     */
    public function testGetters()
    {
        $object = new Metadata(
            $extension = '.jpg',
            $mimeType = 'image/jpeg',
            $headers = new ArrayCollection(['header' => 'value']),
            $metadata = new ArrayCollection(['extra' => 'data'])
        );

        $this->assertEquals($extension, $object->getExtension());
        $this->assertEquals($mimeType, $object->getMimeType());
        $this->assertEquals($headers->toArray(), $object->getHeaders()->toArray());
        $this->assertEquals($metadata->toArray(), $object->getMetadata()->toArray());
    }

    /**
     * Test the StorageData method.
     */
    public function testGetStorageDataMethod()
    {
        $metadata = new Metadata(
            'ext',
            $mimeType = 'application/ext',
            $headers = new ArrayCollection(['Header' => 'ShouldBeMerged']),
            $extra = new ArrayCollection(['Content-Bucket' => 'disposable'])
        );
        $result = $metadata->storageData()->toArray();

        $this->assertArrayHasKey('headers', $result);

        // MimeType must be added to the headers for cloud (amazon atleast) storage
        $this->assertContains($mimeType, $result['headers']);
        $this->assertArrayHasKey('Content-Type', $result['headers']);

        // It should be merged with the default
        foreach ($headers->toArray() as $key => $value) {
            $this->assertSame($value, $result['headers'][$key]);
        }

        // These must be somewhere in the result
        foreach ($extra->toArray() as $key => $value) {
            $this->assertSame($value, $result[$key]);
        }
    }
}
