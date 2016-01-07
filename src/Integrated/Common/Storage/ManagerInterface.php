<?php

namespace Integrated\Common\Storage;

use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;
use Integrated\Common\Storage\Command\CommandInterface;
use Integrated\Common\Storage\Handler\QueuedCommandBusInterface;
use Integrated\Common\Storage\Reader\ReaderInterface;

use Doctrine\Common\Collections\ArrayCollection;
use Psr\Log\LoggerInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
interface ManagerInterface
{
    /**
     * @const string
     */
    const LOG_PREFIX = 'IntegratedStorage: ';

    /**
     * The (queued) command (message) bus strategy
     *
     * @param CommandInterface $command
     */
    public function handle(CommandInterface $command);

    /**
     * Fast and simple read action (without a promise)
     *
     * @param StorageInterface $storage
     * @throws \LogicException
     * @return string
     */
    public function read(StorageInterface $storage);

    /**
     * @param ReaderInterface $reader
     * @param ArrayCollection $filesystems
     * @return StorageInterface
     * @throws \Exception
     */
    public function write(ReaderInterface $reader, ArrayCollection $filesystems = null);

    /**
     * A new storage object (make sure you update it)
     *
     * @param StorageInterface $storage
     * @param ReaderInterface $reader
     * @return ManagerInterface
     */
    public function update(StorageInterface $storage, ReaderInterface $reader);

    /**
     * @param StorageInterface $storage
     * @param ArrayCollection $filesystems
     * @return StorageInterface
     */
    public function delete(StorageInterface $storage, ArrayCollection $filesystems = null);

    /**
     * Copy the storage object to any other filesystem
     *
     * @param StorageInterface $storage
     * @param ArrayCollection $filesystems
     * @return StorageInterface
     */
    public function copy(StorageInterface $storage, ArrayCollection $filesystems);
}