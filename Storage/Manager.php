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

use Integrated\Bundle\StorageBundle\Document\Embedded\Storage;
use Integrated\Bundle\StorageBundle\Document\Embedded\StorageInterface;
use Integrated\Bundle\StorageBundle\Storage\Command\CommandInterface;
use Integrated\Bundle\StorageBundle\Storage\Exception\RevertException;
use Integrated\Bundle\StorageBundle\Storage\Handler\QueuedCommandBusInterface;
use Integrated\Bundle\StorageBundle\Storage\Reader\MemoryReader;
use Integrated\Bundle\StorageBundle\Storage\Reader\ReaderInterface;
use Integrated\Bundle\StorageBundle\Storage\Registry\FilesystemRegistry;
use Integrated\Bundle\StorageBundle\Storage\Validation\FilesystemValidation;

use Doctrine\Common\Collections\ArrayCollection;
use Gaufrette\Exception\FileNotFound;
use Psr\Log\LoggerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class Manager implements ManagerInterface
{
    /**
     * @var FilesystemRegistry
     */
    protected $registry;

    /**
     * @var LoggerInterface
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
     * {@inheritdoc}
     */
    public function __construct(
        FilesystemRegistry $registry,
        Resolver $resolveStorage,
        LoggerInterface $logger,
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
     * {@inheritdoc}
     */
    public function write(ReaderInterface $reader, ArrayCollection $filesystems = null)
    {
        // Required data for the storage object
        $identifier = $this->resolver->getIdentifier($reader);
        $filesystemMap = new ArrayCollection();

        try {
            $validation = new FilesystemValidation($this->registry);
            foreach ($validation->getValidFilesystems($filesystems) as $key) {
                // Log it
                $this->logger->info(
                    sprintf(
                        '%sGoing to write %s in filesystem %s',
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

            $this->logger->critical(
                sprintf(
                    '%s%s',
                    self::LOG_PREFIX,
                    $e->getMessage()
                )
            );

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
    public function update(StorageInterface $storage, ReaderInterface $reader)
    {
        // An update might change the signature of the key (FileIdentifier)
        $this->delete($storage);

        // Return the new object
        return $this->write($reader);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(StorageInterface $storage, ArrayCollection $filesystems = null)
    {
        // Sanity
        $validator = new FilesystemValidation($this->registry);

        // Delete it in all the known filesystems for the file
        foreach ($validator->getValidFilesystems($filesystems) as $key => $filesystem) {
            try {
                $this->registry->get($filesystem)
                    ->delete($storage->getIdentifier());

                $this->logger->notice(
                    sprintf(
                        '%sFile %s delete from filesystem %s',
                        self::LOG_PREFIX,
                        $storage->getIdentifier(),
                        $key
                    )
                );
            } catch (FileNotFound $e) {
                // Seems like we're not in sync
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

        return Storage::postWrite(
            $storage->getIdentifier(),
            new ArrayCollection(
                array_merge(
                    $storage->getFilesystems()->toArray(),
                    $filesystems->toArray()
                )
            ),
            $this->resolver,
            $storage->getMetadata()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function copy(StorageInterface $storage, ArrayCollection $filesystems)
    {
        $this->write(
            new MemoryReader(
                $this->read($storage),
                $storage->getMetadata()
            ),
            $filesystems
        );

        return Storage::postWrite(
            $storage->getIdentifier(),
            new ArrayCollection(
                array_merge(
                    $storage->getFilesystems()->toArray(),
                    $filesystems->toArray()
                )
            ),
            $this->resolver,
            $storage->getMetadata()
        );
    }
}
