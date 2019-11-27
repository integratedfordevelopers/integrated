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

use Doctrine\Common\Collections\ArrayCollection;
use Integrated\Common\Content\Document\Storage\Embedded\MetadataInterface;

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
     * @var string
     */
    protected $credits;

    /**
     * @var string
     */
    protected $description;

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
                [
                    'headers' => array_replace($this->headers, ['Content-Type' => $this->mimeType]),
                ],
                ['credits' => $this->getCredits()],
                ['description' => $this->getDescription()]
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
     * @return ?string
     */
    public function getCredits(): ?string
    {
        return $this->credits;
    }

    /**
     * @param ?string $credits
     */
    public function setCredits(?string $credits): void
    {
        $this->credits = $credits;
    }

    /**
     * @return ?string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param ?string $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
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
