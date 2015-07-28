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

use Integrated\Bundle\StorageBundle\Document\Embedded\Storage;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class StorageTranslation
{
    /**
     * @var Storage
     */
    protected $storage;

    /**
     * @param Storage $storage
     */
    public function __construct(Storage $storage)
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
