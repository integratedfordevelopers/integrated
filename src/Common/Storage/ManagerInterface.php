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
use Integrated\Common\Storage\Command\CommandInterface;
use Integrated\Common\Storage\Reader\ReaderInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
interface ManagerInterface
{
    /**
     * @const string
     */
    public const LOG_PREFIX = 'IntegratedStorage: ';

    /**
     * The (queued) command (message) bus strategy.
     *
     * @param CommandInterface $command
     */
    public function handle(CommandInterface $command);

    /**
     * Fast and simple read action (without a promise).
     *
     * @param StorageInterface $storage
     *
     * @throws \LogicException
     *
     * @return string
     */
    public function read(StorageInterface $storage);

    /**
     * Write the file in the storage, all filesystems or specified.
     *
     * @param ReaderInterface $reader
     * @param ArrayCollection $filesystems
     *
     * @throws \Exception
     *
     * @return StorageInterface
     */
    public function write(ReaderInterface $reader, ArrayCollection $filesystems = null);

    /**
     * Move the file to the specified filesystems.
     *
     * @param StorageInterface $storage
     * @param ArrayCollection  $filesystems
     *
     * @return StorageInterface
     */
    public function move(StorageInterface $storage, ArrayCollection $filesystems);

    /**
     * Delete the file in all known filesystems.
     *
     * @param StorageInterface $storage
     */
    public function delete(StorageInterface $storage);
}
