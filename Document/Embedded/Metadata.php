<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Document\Embedded;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 * @ODM\EmbeddedDocument
 */
class Metadata
{
    /**
     * @var string
     * @ODM\String
     */
    protected $extension;

    /**
     * @var string
     * @ODM\String
     */
    protected $mimeType;

    /**
     * @var array
     * @ODM\Hash
     */
    protected $headers = [];

    /**
     * @var array
     * @ODM\Hash
     */
    protected $metadata = [];

    /**
     * @param string $extension
     * @param string $mimeType
     * @param array $headers
     * @param array $metadata
     */
    public function __construct($extension, $mimeType, $headers = [], $metadata = [])
    {
        $this->extension = $extension;
        $this->mimeType = $mimeType;
        $this->headers = $headers;
        $this->metadata = $metadata;
    }

    /**
     * @return array
     */
    public function storageData()
    {
        return array_merge_recursive(
            $this->metadata,
            ['headers' =>
                array_replace(
                    $this->headers,
                    ['Content-Type' => $this->mimeType]
                )
            ]
        );
    }

    /**
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return array
     */
    public function getMetadata()
    {
        return $this->metadata;
    }
}
