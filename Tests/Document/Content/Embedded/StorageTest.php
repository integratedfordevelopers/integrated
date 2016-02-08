<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Integrated\Bundle\ContentBundle\Tests\Document\Content\Embedded;

use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Storage;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class StorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the post write method used to create an object
     */
    public function testPostWriteMethod()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Integrated\Common\Storage\ResolverInterface $resolver */
        $resolver = $this->getMock('Integrated\Common\Storage\ResolverInterface');
        $resolver->expects($this->once())
            ->method('resolve')
            ->willReturn($path = md5(time()));


        $object = Storage::postWrite(
            $identifier = md5(time()),
            $filesystems = new ArrayCollection(['local', 'private']),
            $resolver,
            $metadata = new Storage\Metadata(
                '.jpg',
                'image/jpeg',
                new ArrayCollection(),
                new ArrayCollection()
            )
        );

        $this->assertEquals($path, (string) $object);
        $this->assertEquals($identifier, $object->getIdentifier());
        $this->assertEquals($path, $object->getPathname());
        $this->assertEquals($filesystems, $object->getFilesystems());
        $this->assertEquals($metadata, $object->getMetadata());
    }
}
