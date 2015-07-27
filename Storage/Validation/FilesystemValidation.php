<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Storage\Validation;

use Integrated\Bundle\StorageBundle\Storage\Registry\FilesystemRegistry;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class FilesystemValidation
{
    /**
     * @var FilesystemRegistry
     */
    protected $registry;

    /**
     * @param FilesystemRegistry $registry
     */
    public function __construct(FilesystemRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Returns a valid list of filesystems
     *
     * @param array $filesystems
     * @throws \InvalidArgumentException
     * @return array $filesystems
     */
    public function isValid(array $filesystems)
    {
        if (0 == count($filesystems)) {
            $filesystems = $this->registry->keys();
        }

        foreach ($filesystems as $key) {
            if (!$this->registry->exists($key)) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'The filesystem %s does not exist.',
                        $key
                    )
                );
            }
        }

        return $filesystems;
    }
}
