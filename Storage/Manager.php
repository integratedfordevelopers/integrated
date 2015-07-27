<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Storage;

use Gaufrette\Exception\FileNotFound;
use Integrated\Bundle\StorageBundle\Document\Embedded\Storage;
use Integrated\Bundle\StorageBundle\Storage\Command\CommandInterface;
use Integrated\Bundle\StorageBundle\Storage\Exception\RevertException;
use Integrated\Bundle\StorageBundle\Storage\Handler\QueuedCommandBusInterface;
use Integrated\Bundle\StorageBundle\Storage\Reader\MemoryReader;
use Integrated\Bundle\StorageBundle\Storage\Reader\ReaderInterface;
use Integrated\Bundle\StorageBundle\Storage\Registry\FilesystemRegistry;
use Integrated\Bundle\StorageBundle\Storage\Validation\FilesystemValidation;
use Monolog\Logger;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class Manager
{
    /**
     * @const string
     */
    const LOG_PREFIX = 'IntegratedStorage: ';

    /**
     * @var FilesystemRegistry
     */
    protected $registry;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var Resolver
     */
    protected $resolver;

    /**
     * @var QueuedCommandBusInterface
     */
    protected $commandBus;

    /**
     * @param FilesystemRegistry $registry
     * @param Resolver $resolveStorage
     * @param Logger $logger
     * @param QueuedCommandBusInterface $busInterface
     */
    public function __construct(
        FilesystemRegistry $registry,
        Resolver $resolveStorage,
        Logger $logger,
        QueuedCommandBusInterface $busInterface = null
    )
    {
        $this->registry = $registry;
        $this->resolver = $resolveStorage;
        $this->logger = $logger;
        $this->commandBus = $busInterface;
    }

    /**
     * The (queued) command (message) bus strategy
     *
     * @param CommandInterface $command
     */
    public function handle(CommandInterface $command)
    {
        if (null == $this->commandBus) {
            $command->execute($this);
        } else {
            $this->commandBus->add($command);
        }
    }

    /**
     * Fast and simple read action (without a promise)
     *
     * @param Storage $storage
     * @throws \LogicException
     * @return string
     */
    public function read(Storage $storage)
    {
        foreach ($storage->getFilesystems() as $filesystem) {
            // A filesystem might be down, walk over all to get the best candidate
            $_filesystem = $this->registry->get($filesystem);

            // The file must exist on the storage (might be cached), this is a sanity check
            if ($_filesystem->has($storage->getIdentifier())) {
                return $_filesystem->read($storage->getIdentifier());
            }
        }

        throw new \LogicException(
            sprintf(
                'The file %s has no available filesystem for a read operation.',
                $storage->getIdentifier()
            )
        );
    }

    /**
     * @param ReaderInterface $reader
     * @param array $filesystems
     * @return Storage
     * @throws RevertException
     * @throws \Exception
     */
    public function write(ReaderInterface $reader, $filesystems = [])
    {
        // Required data for the storage object
        $identifier = $this->resolver->getIdentifier($reader);
        $filesystemMap = [];

        try {
            $validation = new FilesystemValidation($this->registry);
            foreach ($validation->isValid($filesystems) as $key) {

                // Log it
                $this->logger->log(Logger::INFO,
                    sprintf('%sGoing to write %s in filesystem %s',
                        self::LOG_PREFIX,
                        $identifier,
                        $key
                    )
                );

                // Get the filesystem from the registry
                $filesystem = $this->registry->get($key);

                // Check for existence, do not continue if it exists. A file may have updated meta data
                if ($filesystem->has($identifier)) {
                    $storage = $filesystem->get($identifier);
                } else {
                    $storage = $filesystem->createFile($identifier);
                }

                // Might return 0 for an empty file (or throw an exception)
                if (false === $storage->setContent($reader->read(), $reader->getMetadata()->storageData())) {
                    // Throw a roll back
                    throw new RevertException(
                        sprintf(
                            '%sThe filesystem %s denied writing for key %s',
                            self::LOG_PREFIX,
                            $key,
                            $identifier
                        )
                    );
                }

                // Attach the filesystem to the storage object
                $filesystemMap[] = $key;
            }
        } catch (Exception $e) {
            // Before bubbling up the catch conditions revert any changes to the file system
            foreach ($filesystemMap as $key) {
                // Issue the delete request, no validation
                $this->registry->get($key)->delete($identifier);
            }

            $this->logger->log(LOGGER::CRITICAL, sprintf('%s%s', self::LOG_PREFIX, $e->getMessage()));

            throw $e;
        }

        // Return a storage object used in the database
        return Storage::postWrite(
            $identifier,
            $filesystemMap,
            $this->resolver,
            $reader->getMetadata()
        );
    }

    /**
     * A new storage object (make sure you update it)
     *
     * @param Storage $storage
     * @param ReaderInterface $reader
     * @return Manager
     */
    public function update(Storage $storage, ReaderInterface $reader)
    {
        // An update might change the signature of the key (FileIdentifier)
        $this->delete($storage);

        // Return the new object
        return $this->write($reader);
    }

    /**
     * @param Storage $storage
     * @param array $filesystems
     * @return Storage
     */
    public function delete(Storage $storage, $filesystems = [])
    {
        // Delete it everywhere or only one
        if (0 == count($filesystems)) {
            $filesystems = $storage->getFilesystems();
        }

        // Sanity
        $validator = new FilesystemValidation($this->registry);

        // Delete it in all the known filesystems for the file
        foreach ($validator->isValid($filesystems) as $key => $filesystem) {
            try {
                $this->registry->get($filesystem)
                    ->delete($storage->getIdentifier());

                $this->logger->log(Logger::NOTICE,
                    sprintf(
                        '%sFile %s delete from filesystem %s',
                        self::LOG_PREFIX,
                        $storage->getIdentifier(),
                        $key
                    )
                );
            } catch (FileNotFound $e) {
                // Seems like we're not in sync
                $this->logger->log(Logger::ERROR,
                    sprintf(
                        '%sRemote filesystem %s does not contain %s file',
                        self::LOG_PREFIX,
                        $key,
                        $storage->getIdentifier()
                    )
                );
            }
        }

        return Storage::updateFilesystems(
            $storage,
            array_diff($storage->getFilesystems(), $filesystems)
        );
    }

    /**
     * Copy the storage object to any other filesystem
     *
     * @param Storage $storage
     * @param array $filesystems
     * @return Storage
     */
    public function copy(Storage $storage, $filesystems)
    {
        // Reader
        $reader = new MemoryReader(
            $this->read($storage),
            $storage->getMetadata()
        );

        $this->write($reader, $filesystems);

        return Storage::updateFilesystems(
            $storage,
            array_merge($storage->getFilesystems(), $filesystems)
        );
    }
}
