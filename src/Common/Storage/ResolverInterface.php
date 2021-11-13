<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Storage;

use Doctrine\Common\Collections\ArrayCollection;
use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;
use Integrated\Common\Storage\Reader\ReaderInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
interface ResolverInterface
{
    /**
     * Gives you an absolute path to the storage.
     * A preference can be given. When the preference is not able to serve the file another filesystem will be used.
     *
     * @param StorageInterface     $storage
     * @param ArrayCollection|null $filesystem
     *
     * @return string absolute path
     */
    public function resolve(StorageInterface $storage, ArrayCollection $filesystem = null);

    /**
     * @param ReaderInterface $reader
     *
     * @return string
     */
    public function getIdentifier(ReaderInterface $reader);

    /**
     * @param $filesystem
     *
     * @return array
     */
    public function getOptions($filesystem);

    /**
     * Create a resolver class based on the options.
     *
     * @param string $filesystem
     * @param string $identifier
     *
     * @return \Integrated\Common\Storage\FileResolver\FileResolverInterface
     */
    public function getResolverClass($filesystem, $identifier);
}
