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

use Doctrine\Common\Collections\ArrayCollection;
use Gaufrette\Exception\FileNotFound;
use Gaufrette\Filesystem;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Storage;
use Integrated\Bundle\StorageBundle\Exception\NoFilesystemAvailableException;
use Integrated\Bundle\StorageBundle\Exception\RevertException;
use Integrated\Bundle\StorageBundle\Storage\Filesystem\WriteFilesystem;
use Integrated\Bundle\StorageBundle\Storage\Reader\MemoryReader;
use Integrated\Bundle\StorageBundle\Storage\Validation\FilesystemValidation;
use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;
use Integrated\Common\Storage\Command\CommandInterface;
use Integrated\Common\Storage\FilesystemRegistryInterface;
use Integrated\Common\Storage\Handler\QueuedCommandBusInterface;
use Integrated\Common\Storage\ManagerInterface;
use Integrated\Common\Storage\Reader\ReaderInterface;
use Integrated\Common\Storage\ResolverInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class Manager implements ManagerInterface
{
    /**
     * @var FilesystemRegistryInterface
     */
    protected $registry;

    /**
     * @var LoggerInterface|null
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
     * @param FilesystemRegistryInterface    $registry
     * @param ResolverInterface              $resolveStorage
     * @param LoggerInterface|null           $logger
     * @param QueuedCommandBusInterface|null $busInterface
     */
    public function __construct(
        FilesystemRegistryInterface $registry,
        ResolverInterface $resolveStorage,
        LoggerInterface $logger = null,
        QueuedCommandBusInterface $busInterface = null
    ) {
        $this->registry = $registry;
        $this->resolver = $resolveStorage;
        $this->logger = $logger;
        $this->commandBus = $busInterface;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function read(StorageInterface $storage)
    {
        // Walk over all filesystems that should contain the file
        foreach ($storage->getFilesystems() as $key) {
            // Just log it, when the fan is dirty (shit hit it)
            try {
                // A filesystem might be down, walk over all to get the best candidate
                $filesystem = $this->registry->get($key);

                // The file must exist on the storage (might be cached), this is a sanity check
                if ($filesystem->has($storage->getIdentifier())) {
                    return $filesystem->read($storage->getIdentifier());
                }
            } catch (Exception $e) {
                if ($this->logger) {
                    $this->logger->alert(
                        sprintf(
                            '%sThe filesystem %s did not properly return the file %s: %s',
                            self::LOG_PREFIX,
                            $e->getMessage()
                        )
                    );
                }
            }
        }

        throw NoFilesystemAvailableException::readOperation($storage);
    }

    /**
     * {@inheritdoc}
     */
    public function write(ReaderInterface $reader, ArrayCollection $filesystems = null)
    {
        // Required data for the storage object
        $identifier = $this->resolver->getIdentifier($reader);
        $filesystemMap = new ArrayCollection();

        try {
            $filesystems = (new FilesystemValidation($this->registry))->getValidFilesystems($filesystems);
            if (0 == $filesystems->count()) {
                throw new \LogicException('A file must be at least on one filesystem');
            }

            foreach ($filesystems as $key) {
                // Log it
                if ($this->logger) {
                    $this->logger->info(
                        sprintf(
                            '%sGoing to write %s in filesystem %s',
                            self::LOG_PREFIX,
                            $identifier,
                            $key
                        )
                    );
                }

                // Check for existence, do not continue if it exists. A file may have updated meta data
                $result = (new WriteFilesystem($this->getFilesystem($key)))->write($identifier, $reader);

                // The method may return false, an identical operator must be used
                if (false === $result) {
                    // Throw a roll back
                    throw RevertException::writeFailed($key, $identifier);
                }

                // Attach the filesystem to the storage object
                $filesystemMap[] = $key;
            }
        } catch (Exception $e) {
            // Attempt to log it, then just pass along
            if ($this->logger) {
                $this->logger->critical(
                    sprintf(
                        '%s%s',
                        self::LOG_PREFIX,
                        $e->getMessage()
                    )
                );
            }

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
     * {@inheritdoc}
     */
    public function delete(StorageInterface $storage)
    {
        // Delete it in all the known filesystems for the file
        foreach ($storage->getFilesystems() as $key) {
            // Try per filesystem, not the file as a whole
            try {
                // Remove the file out the filesystem
                $this->registry->get($key)->delete($storage->getIdentifier());

                if ($this->logger) {
                    $this->logger->notice(
                        sprintf(
                            '%sFile %s delete from filesystem %s',
                            self::LOG_PREFIX,
                            $storage->getIdentifier(),
                            $key
                        )
                    );
                }
            } catch (FileNotFound $e) {
                // Seems like we're not in sync
                if ($this->logger) {
                    $this->logger->error(
                        sprintf(
                            '%sRemote filesystem %s does not contain %s file',
                            self::LOG_PREFIX,
                            $key,
                            $storage->getIdentifier()
                        )
                    );
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function move(StorageInterface $storage, ArrayCollection $filesystems)
    {
        // The file, read by ourselves
        $reader = new MemoryReader(
            $this->read($storage),
            $storage->getMetadata()
        );

        // We'll need one atleast
        if ($filesystems->count()) {
            try {
                // Place the file in the storage
                foreach ($filesystems as $key) {
                    // Place the file
                    $result = (new WriteFilesystem($this->getFilesystem($key)))
                        ->write(
                            $storage->getIdentifier(),
                            $reader
                        )
                    ;

                    if (false === $result) {
                        // Throw a roll back
                        throw RevertException::writeFailed($key, $storage);
                    }
                }

                // Build a map
                $removeMap = new ArrayCollection($storage->getFilesystems());
                $removeMap->filter(
                    function ($key) use ($filesystems) {
                        // Only delete the file from the filesystems that are not requested
                        return false == $filesystems->exists($key);
                    }
                );

                // Remove it
                foreach ($removeMap as $key) {
                    $this->getFilesystem($key)->delete($storage->getIdentifier());
                }

                // Return a new storage object
                return Storage::postWrite(
                    $storage->getIdentifier(),
                    $filesystems,
                    $this->resolver,
                    $storage->getMetadata()
                );
            } catch (RevertException $e) {
                // Just log it
                if ($this->logger) {
                    $this->logger->critical(
                        sprintf(
                            '%s%s',
                            self::LOG_PREFIX,
                            $e->getMessage()
                        )
                    );
                }

                throw $e;
            }
        }

        // No filesystem defined
        throw new \LogicException(
            sprintf(
                'No filesystems to defined to move the file %s to.',
                $storage->getIdentifier()
            )
        );
    }

    /**
     * @param string $key
     *
     * @return Filesystem
     */
    protected function getFilesystem($key)
    {
        // Grab it from reggie
        $filesystem = $this->registry->get($key);
        if ($filesystem instanceof Filesystem) {
            return $filesystem;
        }

        // We must return some sort specialization like Filesystem ainit?
        throw new \LogicException(
            sprintf(
                'A instanceof Gaufrette\Filesystem was expected (given: %s).',
                \is_object($filesystem) ? \get_class($filesystem) : \gettype($filesystem)
            )
        );
    }
}
