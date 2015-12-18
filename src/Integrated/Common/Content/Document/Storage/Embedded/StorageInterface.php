<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Document\Storage\Embedded;

use Integrated\Common\Content\Document\Storage\Embedded\MetadataInterface;
use Integrated\Common\Storage\ResolverInterface;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * This document covers the database existence, it is not allowed to create a new instance manually.
 * The filesystem is responsible for the file creation on the system(s) it self.
 * Any changes to the filesystem must be passed by a command trough the manager handler.
 *
 * @author Johnny Borg <johnny@e-active.nl>
 */
interface StorageInterface
{
    /**
     * Create an object after a write operation on the filesystem
     *
     * @param string $identity
     * @param ArrayCollection $filesystems
     * @param ResolverInterface $resolverStorage
     * @param MetadataInterface $metadata
     * @return StorageInterface
     */
    public static function postWrite(
        $identity,
        ArrayCollection $filesystems,
        ResolverInterface $resolverStorage,
        MetadataInterface $metadata
    );

    /**
     * @return string
     */
    public function getIdentifier();

    /**
     * @return string
     */
    public function getPathname();

    /**
     * @return ArrayCollection
     */
    public function getFilesystems();

    /**
     * @return MetadataInterface
     */
    public function getMetadata();
}
