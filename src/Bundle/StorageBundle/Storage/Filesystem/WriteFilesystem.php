<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Storage\Filesystem;

use Gaufrette\File;
use Gaufrette\Filesystem;
use Integrated\Common\Storage\Reader\ReaderInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class WriteFilesystem
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @param string          $identifier
     * @param ReaderInterface $reader
     *
     * @throws \LogicException
     *
     * @return mixed
     */
    public function write($identifier, ReaderInterface $reader)
    {
        // Get the file (or create)
        if ($this->filesystem->has($identifier)) {
            $storage = $this->filesystem->get($identifier);
        } else {
            $storage = $this->filesystem->createFile($identifier);
        }

        // Write her, if you can
        if ($storage instanceof File) {
            return $storage->setContent(
                $reader->read(),
                $reader->getMetadata()->storageData()->toArray()
            );
        }

        // Well that escalated quickly
        throw new \LogicException(
            sprintf(
                'A instanceof Gaufrette\File was excepted (given: %s).',
                \get_class($storage)
            )
        );
    }
}
