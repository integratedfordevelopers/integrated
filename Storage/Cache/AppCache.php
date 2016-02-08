<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Bundle\StorageBundle\Storage\Cache;

use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;
use Integrated\Common\Storage\Cache\CacheInterface;
use Integrated\Common\Storage\ManagerInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class AppCache implements CacheInterface
{
    /**
     * @const
     */
    const CACHE_PATH = '%s/integrated/storage/%s';

    /**
     * @var ManagerInterface
     */
    protected $fileManager;

    /**
     * @var string
     */
    protected $directory;

    /**
     * @param string $directory
     * @param ManagerInterface $managerInterface
     */
    public function __construct($directory, ManagerInterface $managerInterface)
    {
        $this->fileManager = $managerInterface;
        $this->directory = $directory;
    }

    /**
     * {@inheritdoc}
     */
    public function path(StorageInterface $storage)
    {
        // Attempt to make a local copy of the file, it's probably not in a public storage
        $file = new \SplFileInfo(
            sprintf(
                // Directory
                self::CACHE_PATH,
                $this->directory,
                // The path in cache directory
                sprintf(
                    '%s/%s/%s',
                    substr($storage->getIdentifier(), 0, 2),
                    substr($storage->getIdentifier(), 2, 2),
                    $storage->getIdentifier()
                )
            )
        );

        // Check if a file exists
        if ($file->isFile()) {
            return $file->getPathname();
        }

        // Create the directory
        $directories = explode('/', substr(str_replace($this->directory, '', $file->getPath()), 1));
        for ($i = 1; $i <= count($directories); $i++) {
            $dir = sprintf('%s/%s', $this->directory, implode('/', array_slice($directories, 0, $i)));

            if (!is_dir($dir)) {
                mkdir($dir);
            }
        }

        // Open a file with write permission
        $write = $file->openFile('w');
        if ($write->isWritable()) {
            // Write it
            $write->fwrite(
                $this->fileManager->read($storage)
            );

            return $write->getPathname();
        }

        // Let's give it to the requestee, we failed
        throw new \LogicException(
            'The directory %s is not writable or the cache directory does not exist.',
            $this->directory
        );
    }
}
