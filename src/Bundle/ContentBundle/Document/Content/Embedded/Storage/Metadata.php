<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Document\Content\Embedded\Storage;

use Integrated\Common\Content\Document\Storage\Embedded\MetadataInterface;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class Metadata implements MetadataInterface
{
    /**
     * @var string
     */
    protected $extension;

    /**
     * @var string
     */
    protected $mimeType;

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var array
     */
    protected $metadata = [];

    /**
     * {@inheritdoc}
     */
    public function __construct($extension, $mimeType, ArrayCollection $headers, ArrayCollection $metadata)
    {
        $this->extension = $extension;
        $this->mimeType = $mimeType;
        $this->headers = $headers->toArray();
        $this->metadata = $metadata->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function storageData()
    {
        return new ArrayCollection(
            array_merge_recursive(
                $this->metadata,
                ['headers' =>
                    array_replace(
                        $this->headers,
                        ['Content-Type' => $this->mimeType]
                    )
                ]
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * {@inheritdoc}
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders()
    {
        return new ArrayCollection($this->headers);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata()
    {
        return new ArrayCollection($this->metadata);
    }
}
