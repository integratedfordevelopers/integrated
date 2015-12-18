<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Document\Storage\Embedded;

use Integrated\Common\Content\Document\Storage\Embedded\MetadataInterface;
use Integrated\Common\Document\Storage\Embedded\StorageInterface;
use Integrated\Common\Storage\ResolverInterface;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * This document covers the database existence, it is not allowed to create a new instance manually.
 * The filesystem is responsible for the file creation on the system(s) it self.
 * Any changes to the filesystem must be passed by a command trough the manager handler.
 *
 * @author Johnny Borg <johnny@e-active.nl>
 * @ODM\EmbeddedDocument
 */
class Storage implements StorageInterface
{
    /**
     * @var string
     * @ODM\Index
     * @ODM\String
     */
    protected $identifier;

    /**
     * @var string
     * @ODM\String
     */
    protected $pathname;

    /**
     * @var Metadata
     * @ODM\EmbedOne(targetDocument="Integrated\Bundle\ContentBundle\Document\Storage\Embedded")
     */
    protected $metadata;

    /**
     * @var ArrayCollection
     * @ODM\Collection
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
    public function __toString()
    {
        return (string) $this->pathname;
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
