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

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class DirectoryUtil
{
    /**
     * @param string $mount
     * @param string $directory
     */
    public static function createDirectory($mount, $directory)
    {
        // Create the directory
        $directories = explode('/', str_replace($mount, '', $directory));
        for ($i = 1; $i <= count($directories); $i++) {
            $dir = sprintf('%s/%s', $mount, implode('/', array_slice($directories, 0, $i)));

            if (!is_dir($dir)) {
                mkdir($dir);
            }
        }
    }
}
