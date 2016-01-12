<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Storage\Database\Translation;

use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class StorageTranslation
{
    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @param StorageInterface $storage
     */
    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'identifier' => $this->storage->getIdentifier(),
            'pathname' => $this->storage->getPathname(),
            'metadata' => (new MetadataTranslation($this->storage->getMetadata()))->toArray(),
            'filesystems' => $this->storage->getFilesystems()
        ];
    }
}
