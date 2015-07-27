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

use Integrated\Bundle\StorageBundle\Storage\Resolver;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * This document covers the database existence, it is not allowed to create a new instance manually.
 * The filesystem is responsible for the file creation on the system(s) it self.
 * Any changes to the filesystem must be passed by a command trough the manager handler.
 *
 * @author Johnny Borg <johnny@e-active.nl>
 * @ODM\EmbeddedDocument
 */
class Storage
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
     * @ODM\EmbedOne(targetDocument="Integrated\Bundle\StorageBundle\Document\Embedded\Metadata")
     */
    protected $metadata;

    /**
     * @var array
     * @ODM\Collection
     */
    protected $filesystems = [];

    /**
     * Instance creation with new is prohibited, see the class header.
     */
    protected function __construct() {}

    /**
     * Create an object after a write operation on the filesystem
     *
     * @param string $identity
     * @param array $filesystems
     * @param Resolver $resolverStorage
     * @param Metadata $metadata
     * @return Storage
     */
    public static function postWrite($identity, array $filesystems, Resolver $resolverStorage, Metadata $metadata)
    {
        $self = new static();
        $self->identifier = $identity;
        $self->filesystems = $filesystems;
        $self->metadata = $metadata;
        $self->pathname = $resolverStorage->resolve($self);

        return $self;
    }

    /**
     * Update the filesystem data
     *
     * @param Storage $storage
     * @param $filesystems
     * @return Storage
     */
    public static function updateFilesystems(Storage $storage, $filesystems)
    {
        $storage->filesystems = $filesystems;
        return $storage;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->pathname;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function getPathname()
    {
        return $this->pathname;
    }

    /**
     * @return array
     */
    public function getFilesystems()
    {
        return $this->filesystems;
    }

    /**
     * @return Metadata
     */
    public function getMetadata()
    {
        return $this->metadata;
    }
}
