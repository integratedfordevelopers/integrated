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

use Integrated\Bundle\StorageBundle\Storage\Reader\MemoryReader;
use Integrated\Bundle\StorageBundle\Storage\Reflection\Document\DoctrineDocument;
use Integrated\Bundle\StorageBundle\Storage\Reflection\ReflectionCacheInterface;

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
    const ADD = 'add';

    /**
     * @const string
     */
    const REMOVE = 'remove';

    /**
     * @param ManagerInterface $storage
     * @param ReflectionCacheInterface $reflection
     * @param string $filesystem
     * @return \Closure
     */
    public static function remove(ManagerInterface $storage, ReflectionCacheInterface $reflection, $filesystem)
    {
        return static::modifyFilesystem($storage, $reflection, $filesystem, static::REMOVE);
    }

    /**
     * @param ManagerInterface $storage
     * @param ReflectionCacheInterface $reflection
     * @param string $filesystem
     * @return \Closure
     */
    public static function add(ManagerInterface $storage, ReflectionCacheInterface $reflection, $filesystem)
    {
        return static::modifyFilesystem($storage, $reflection, $filesystem, static::ADD);
    }

    /**
     * @param ManagerInterface $storage
     * @param ReflectionCacheInterface $reflection
     * @param $filesystem
     * @param $operation
     * @return \Closure
     */
    protected static function modifyFilesystem(ManagerInterface $storage, ReflectionCacheInterface $reflection, $filesystem, $operation)
    {
        return function(DoctrineDocument $document) use ($storage, $reflection, $operation, $filesystem) {
            foreach ($reflection->getPropertyReflectionClass($document->getClassName())->getTargetProperties() as $property) {
                /** @var StorageInterface|bool $file */
                if ($file = $document->get($property->getPropertyName())) {
                    // Do the operation on the collection
                    $filesystems = $file->getFilesystems();
                    switch ($operation) {
                        case self::ADD:
                            $filesystems->contains($filesystem) ?: $filesystems->add($filesystem);
                            break;
                        case self::REMOVE:
                            $filesystems->remove($filesystem);
                            break;
                        default:
                            throw new \RuntimeException(sprintf('Operation %s for filesystem does not exist', $operation));
                    }

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
        };
    }
}
