<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ImageBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

use Integrated\Bundle\ContentBundle\Extension\LocatableStorageInterfaceTrait;
use Integrated\Common\Content\Document\Storage\Embedded\MetadataInterface;
use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class StorageModel implements StorageInterface, \ArrayAccess
{
    use LocatableStorageInterfaceTrait;

    /**
     * @var string
     */
    private $identifier;

    /**
     * @var string
     */
    private $pathname;

    /**
     * @var ArrayCollection
     */
    private $filesystems;

    /**
     * @var MetadataInterface
     */
    private $metadata;

    /**
     * @param string $identifier
     * @param string $pathname
     * @param ArrayCollection $filesystems
     * @param MetadataInterface $metadata
     */
    public function __construct($identifier, $pathname, ArrayCollection $filesystems, MetadataInterface $metadata)
    {
        $this->identifier = $identifier;
        $this->pathname = $pathname;
        $this->filesystems = $filesystems;
        $this->metadata = $metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function getPathname()
    {
        return $this->pathname;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilesystems()
    {
        return $this->filesystems;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata()
    {
        return $this->metadata;
    }
}
