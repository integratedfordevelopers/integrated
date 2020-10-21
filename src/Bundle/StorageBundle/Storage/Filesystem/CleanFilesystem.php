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

use Integrated\Bundle\StorageBundle\Storage\Registry\FilesystemRegistry;
use Integrated\Common\Storage\Database\DatabaseInterface;

class CleanFilesystem
{
    /**
     * @var FilesystemRegistry
     */
    private $registry;

    /**
     * @var DatabaseInterface
     */
    private $database;

    /**
     * @param FilesystemRegistry $registry
     * @param DatabaseInterface  $database
     */
    public function __construct(FilesystemRegistry $registry, DatabaseInterface $database)
    {
        $this->registry = $registry;
        $this->database = $database;
    }

    /**
     * Finds unused files in the storage and moves them to the given directory
     * When the directory is not specified just the list with cleanable files
     * is returned
     *
     * @param string      $identifier
     * @param string|null $moveDirectory
     *
     * @return array
     */
    public function clean(string $identifier, ?string $moveDirectory = null)
    {
        $filesystem = $this->registry->get($identifier);

        $cleanable = [];

        $keys = $filesystem->listKeys();
        $keys = array_flip($keys['keys']);

        $objects = $this->database->getStorageKeys();

        if ($moveDirectory && !is_dir($moveDirectory)) {
            throw new \RuntimeException(sprintf('Directory %s does not exists', $moveDirectory));
        }

        foreach ($keys as $key => $value) {
            if (substr($key, 0, 1) === '.') {
                continue;
            }

            if (isset($objects[$key])) {
                continue;
            }

            $cleanable[] = $key;

            if (!$moveDirectory) {
                continue;
            }

            $targetFile = rtrim($moveDirectory, '/').'/'.$key;
            if (file_exists($targetFile)) {
                throw new \RuntimeException(sprintf('File %s does already exists', $targetFile));
            }

            if (file_put_contents($targetFile, $filesystem->read($key)) !== false) {
                $filesystem->delete($key);
            }
        }

        return $cleanable;
    }
}
