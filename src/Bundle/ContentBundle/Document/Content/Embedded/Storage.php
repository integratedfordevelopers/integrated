<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Document\Content\Embedded;

use Integrated\Bundle\ContentBundle\Extension\LocatableStorageInterfaceTrait;
use Integrated\Common\Content\Document\Storage\Embedded\MetadataInterface;
use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;
use Integrated\Common\Storage\ResolverInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * This document covers the database existence, it is not allowed to create a new instance manually.
 * The filesystem is responsible for the file creation on the system(s) it self.
 * Any changes to the filesystem must be passed by a command trough the manager handler.
 *
 * @author Johnny Borg <johnny@e-active.nl>
 */
class Storage implements StorageInterface, \ArrayAccess
{
    use LocatableStorageInterfaceTrait;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var string
     */
    protected $pathname;

    /**
     * @var Storage\Metadata
     */
    protected $metadata;

    /**
     * @var ArrayCollection
     */
    protected $filesystems;

    /**
     * Instance creation with new is prohibited, see the class header.
     */
    final protected function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public static function postWrite(
        $identity,
        ArrayCollection $filesystems,
        ResolverInterface $resolverStorage,
        MetadataInterface $metadata
    ) {
        $self = new static();
        $self->identifier = $identity;
        $self->filesystems = $filesystems->toArray();
        $self->metadata = $metadata;
        $self->pathname = $resolverStorage->resolve($self);

        return $self;
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
        return new ArrayCollection($this->filesystems);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata()
    {
        return $this->metadata;
    }
}
