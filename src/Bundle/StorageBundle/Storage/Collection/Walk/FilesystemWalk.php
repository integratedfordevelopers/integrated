<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Storage\Collection\Walk;

use Integrated\Bundle\StorageBundle\Storage\Accessor\DoctrineDocument;
use Integrated\Bundle\StorageBundle\Storage\Mapping\MetadataFactoryInterface;
use Integrated\Bundle\StorageBundle\Storage\Reader\MemoryReader;
use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;
use Integrated\Common\Storage\ManagerInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class FilesystemWalk
{
    /**
     * @const string
     */
    public const ADD = 'add';

    /**
     * @const string
     */
    public const REMOVE = 'remove';

    /**
     * @param ManagerInterface         $storage
     * @param MetadataFactoryInterface $metadata
     * @param string                   $filesystem
     *
     * @return \Closure
     */
    public static function remove(ManagerInterface $storage, MetadataFactoryInterface $metadata, $filesystem)
    {
        return function (DoctrineDocument $document) use ($storage, $metadata, $filesystem) {
            foreach ($metadata->getMetadata($document->getClassName())->getProperties() as $property) {
                /** @var StorageInterface|bool $file */
                if ($file = $document->get($property->getPropertyName())) {
                    // Get the list
                    $filesystems = $file->getFilesystems();

                    if ($filesystems->contains($filesystem)) {
                        // Remove it
                        $filesystems->removeElement($filesystem);

                        // Update the document
                        $document->set(
                            $property->getPropertyName(),
                            // Let the manager decide which filesystems will be affected
                            $storage->write(
                                new MemoryReader($storage->read($file), $file->getMetadata()),
                                $filesystems
                            )
                        );
                    }
                }
            }
        };
    }

    /**
     * @param ManagerInterface         $storage
     * @param MetadataFactoryInterface $metadata
     * @param string                   $filesystem
     *
     * @return \Closure
     */
    public static function add(ManagerInterface $storage, MetadataFactoryInterface $metadata, $filesystem)
    {
        return function (DoctrineDocument $document) use ($storage, $metadata, $filesystem) {
            foreach ($metadata->getMetadata($document->getClassName())->getProperties() as $property) {
                /** @var StorageInterface|bool $file */
                if ($file = $document->get($property->getPropertyName())) {
                    // Get the list
                    $filesystems = $file->getFilesystems();

                    if (!$filesystems->contains($filesystem)) {
                        // Remove it
                        $filesystems->add($filesystem);

                        // Update the document
                        $document->set(
                            $property->getPropertyName(),
                            // Let the manager decide which filesystems will be affected
                            $storage->write(
                                new MemoryReader($storage->read($file), $file->getMetadata()),
                                $filesystems
                            )
                        );
                    }
                }
            }
        };
    }
}
