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

use Doctrine\Common\Collections\ArrayCollection;
use Integrated\Common\Storage\FilesystemRegistryInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class FilesystemValidation
{
    /**
     * @var FilesystemRegistryInterface
     */
    protected $registry;

    /**
     * Returns a valid list of filesystems
     *
     * @param ArrayCollection $filesystems
     * @return ArrayCollection $filesystems
     * @throws \InvalidArgumentException
     */
    public function getValidFilesystems(ArrayCollection $filesystems = null)
    {
        if (null == $filesystems || 0 == count($filesystems)) {
            $filesystems = new ArrayCollection($this->registry->keys());
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

    /**
     * @param FilesystemRegistryInterface $registry
     */
    public function __construct(FilesystemRegistryInterface $registry)
    {
        $this->registry = $registry;
    }
}
