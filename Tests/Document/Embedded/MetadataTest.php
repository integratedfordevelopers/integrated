<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Tests\Document\Embedded;

use Doctrine\ODM\MongoDB\Tests\Mocks\MetadataDriverMock;
use Integrated\Bundle\StorageBundle\Document\Embedded\Metadata;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class MetadataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the getExtension method
     */
    public function testGetExtensionMethod()
    {
        $metadata = new Metadata($ext = 'ext', '', [], []);

        $this->assertEquals($ext, $metadata->getExtension());
    }

    /**
     * Test the getMimeType method
     */
    public function testGetMimeTypeMethod()
    {
        $metadata = new Metadata('', $mimeType = 'application/ext', [], []);

        $this->assertEquals($mimeType, $metadata->getMimeType());
    }

    /**
     * Test the getHeaders method
     */
    public function testGetHeadersMethod()
    {
        $metadata = new Metadata('', '', $headers = ['key' => 'value'], []);

        $this->assertEquals($headers, $metadata->getHeaders());
    }

    /**
     * Test the getMetadata method
     */
    public function testGetMetadataMethod()
    {
        $metadata = new Metadata('', '', [], $data = ['key' => 'value']);

        $this->assertEquals($data, $metadata->getMetadata());
    }

    /**
     * Test the StorageData method
     */
    public function testGetStorageDataMethod()
    {
        $metadata = new Metadata(
            'ext',
            $mimeType = 'application/ext',
            $headers = ['Header' => 'ShouldBeMerged'],
            $extra = ['Content-Bucket' => 'disposable']
        );
        $result = $metadata->storageData();

        $this->assertArrayHasKey('headers', $result);

        // MimeType must be added to the headers for cloud (amazon atleast) storage
        $this->assertContains($mimeType, $result['headers']);
        $this->assertArrayHasKey('Content-Type', $result['headers']);
        // It should be merged with the deftault
        $this->assertArraySubset($headers, $result['headers'], true);

        // These must be somewhere in the result
        $this->assertArraySubset($extra, $result, true);
    }
}
