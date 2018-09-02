<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Storage\Util;

use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class DirectoryUtil
{
    /**
     * @param string           $directory
     * @param StorageInterface $storage
     * @param string|null      $overwriteExtension
     *
     * @return \SplFileInfo
     */
    public static function cachePathFile($directory, StorageInterface $storage, $overwriteExtension = null)
    {
        // Create the filename
        $file = new \SplFileInfo(
            sprintf(
                '%s/%s/%s/%s',
                $directory,
                substr($storage->getIdentifier(), 0, 2),
                substr($storage->getIdentifier(), 2, 2),
                $overwriteExtension ?: $storage->getIdentifier()
            )
        );

        // Create a directory
        self::createDirectory($file->getPath());

        return $file;
    }

    /**
     * @param string $directory
     *
     * @throws \LogicException
     */
    public static function createDirectory($directory)
    {
        // Skip existing directories
        if (!is_dir($directory)) {
            // Create a directory array
            $directories = explode('/', $directory);

            $max = \count($directories);
            for ($i = 2; $i <= $max; ++$i) {
                $dir = implode('/', \array_slice($directories, 0, $i));

                // You might wanna read is as check as follows: if it exists, make it, check if it did
                if (!is_dir($dir) && !@mkdir($dir) && !is_dir($dir)) {
                    throw new \LogicException(sprintf('Can not create directory %s', $dir));
                }
            }
        }
    }
}
