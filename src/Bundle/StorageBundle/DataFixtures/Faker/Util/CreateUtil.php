<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\DataFixtures\Faker\Util;

use Doctrine\Common\Collections\ArrayCollection;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Storage;
use Integrated\Bundle\StorageBundle\Storage\Reader\MemoryReader;
use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;
use Integrated\Common\Storage\ManagerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class CreateUtil
{
    /**
     * @param ManagerInterface $manager
     * @param string           $path
     *
     * @return StorageInterface
     *
     * @throws \Exception
     */
    public static function path(ManagerInterface $manager, $path)
    {
        // Make sure we've got a winner
        if (file_exists($path)) {
            // Use a reader and set the mime type manually based on the path
            return $manager->write(
                new MemoryReader(
                    // Use the file_get_contents to support local and remote (http) protocols
                    file_get_contents($path),
                    // Metadata
                    new Storage\Metadata(
                        substr($path, strrpos($path, '.') + 1),
                        mime_content_type($path),
                        new ArrayCollection(),
                        new ArrayCollection()
                    )
                )
            );
        }

        // File does not exist or is not readable
        throw new Exception(sprintf('The file %s to put in the storage does not exist', $path));
    }
}
