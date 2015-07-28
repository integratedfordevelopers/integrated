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

use Integrated\Bundle\StorageBundle\Document\Embedded\Metadata;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class MetadataTranslation
{
    /**
     * @var Metadata
     */
    protected $metadata;

    /**
     * @param Metadata $metadata
     */
    public function __construct(Metadata $metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'extension' => $this->metadata->getExtension(),
            'mimeType' => $this->metadata->getMimeType(),
            'headers' => $this->metadata->getHeaders(),
            'metadata' => $this->metadata->getMetadata()
        ];
    }
}
