<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Exception;

use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class NoFilesystemAvailableException extends \ErrorException
{
    /**
     * @param StorageInterface $storage
     *
     * @return NoFilesystemAvailableException
     */
    public static function readOperation(StorageInterface $storage)
    {
        // Just to the last resort
        return new static(
            sprintf(
                'The file %s has no available filesystem(s) for a read operation tried: %s.',
                $storage->getIdentifier(),
                implode(', ', $storage->getFilesystems()->toArray())
            )
        );
    }
}
