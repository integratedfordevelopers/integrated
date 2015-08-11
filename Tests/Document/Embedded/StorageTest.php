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

use Integrated\Bundle\StorageBundle\Document\Embedded\Metadata;
use Integrated\Bundle\StorageBundle\Document\Embedded\Storage;
use Integrated\Bundle\StorageBundle\Storage\Resolver;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class StorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The the postWrite method and getters
     */
    public function testPostWriteMethod()
    {
        $storage = self::createObject(
            $this,
            $identifier = 'identifier',
            $filesystems = ['filesystem1', 'filesystem2'],
            $pathname = 'public.com/path',
            $metadata = new Metadata('ext', 'application/ext')
        );

        $this->assertEquals($identifier, $storage->getIdentifier());
        $this->assertEquals($filesystems, $storage->getFilesystems());
        $this->assertEquals($metadata, $storage->getMetadata());
        $this->assertEquals($pathname, $storage->getPathname());
        $this->assertEquals($pathname, (string) $storage);
    }

    /**
     * Verifies the new filesystem data in an object
     */
    public function testUpdateFilesystemsMethod()
    {
        /** @var Storage $mock */
        $mock = $this->getMockBuilder('Integrated\Bundle\StorageBundle\Document\Embedded\Storage')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        Storage::updateFilesystems(
            $mock,
            $filesystems = ['filesystem1']
        );

        $this->assertEquals($filesystems, $mock->getFilesystems());
    }

    /**
     * Create a valid storage object
     * @param \PHPUnit_Framework_TestCase $testCase
     * @param $identifier
     * @param array $filesystems
     * @param string $pathname
     * @param Metadata|null $metadata
     * @return Storage
     */
    public static function createObject(\PHPUnit_Framework_TestCase $testCase, $identifier, array $filesystems = [], $pathname = 'public.com/path', Metadata $metadata = null)
    {
        /** @var Resolver $resolverMock */
        $resolverMock =
            $testCase->getMockBuilder('Integrated\Bundle\StorageBundle\Storage\Resolver')
                ->disableOriginalConstructor()
                ->getMock();


        // The storage object must resolve at least on public address
        $resolverMock
            ->expects($testCase->once())
            ->method('resolve')
            ->will($testCase->returnValue($pathname))
        ;

        // The object to test
        return Storage::postWrite(
            $identifier,
            $filesystems,
            $resolverMock,
            (null == $metadata ? new Metadata('ext', 'application/ext') : $metadata)
        );
    }
}
