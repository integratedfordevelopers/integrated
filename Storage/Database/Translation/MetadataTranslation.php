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

use Integrated\Common\Content\Document\Storage\Embedded\MetadataInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class MetadataTranslation
{
    /**
     * @var MetadataInterface
     */
    protected $metadata;

    /**
     * @param MetadataInterface $metadata
     */
    public function __construct(MetadataInterface $metadata)
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
