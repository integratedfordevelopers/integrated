<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Storage\Metadata;
use Integrated\Bundle\StorageBundle\Storage\Database\Translation\StorageTranslation;
use Integrated\Bundle\StorageBundle\Storage\Mapping\MetadataFactoryInterface;
use Integrated\Bundle\StorageBundle\Storage\Reader\MemoryReader;
use Integrated\Common\Storage\Database\DatabaseInterface;
use Integrated\Common\Storage\ManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class MigrateCommand extends Command
{
    /**
     * @var DatabaseInterface
     */
    protected $database;

    /**
     * @var ManagerInterface
     */
    protected $storage;

    /**
     * @var MetadataFactoryInterface
     */
    private $metadata;

    /**
     * @param DatabaseInterface        $database
     * @param ManagerInterface         $storage
     * @param MetadataFactoryInterface $metadata
     */
    public function __construct(DatabaseInterface $database, ManagerInterface $storage, MetadataFactoryInterface $metadata)
    {
        $this->database = $database;
        $this->storage = $storage;
        $this->metadata = $metadata;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('storage:migrate')
            ->setDescription('Imports the old file notation in the new notation and places the files in configured storage.')
            ->setHelp('The <info>%command.name%</info> migrates all the old style notation files into the new notation.')
            ->setDefinition([
                new InputArgument(
                    'path',
                    InputArgument::REQUIRED,
                    'A local path where (might have a directroy structure) the files can be resolved'
                ),
                new InputOption(
                    'delete',
                    'd',
                    InputOption::VALUE_NONE,
                    'Delete the local file after placing it in the storage'
                ),
                new InputOption(
                    'ignore-duplicates',
                    'i',
                    InputOption::VALUE_NONE,
                    'Ignore duplicate files errors and grab the newest version'
                ),
                new InputOption(
                    'find-empty',
                    'f',
                    InputOption::VALUE_NONE,
                    'Attempt to find a file if the property is empty'
                ),
            ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Fetch all data from database
        $data = $this->database->getRows();

        // Barry progress
        $progress = new ProgressBar($output, $data->count());
        $progress->start();
        $progress->setFormat('  %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');

        foreach ($data as $row) {
            // Walk over the properties of the class with reflection
            foreach ($this->metadata->getMetadata($row['class'])->getProperties() as $property) {
                // Does the property exists?
                $skip = !isset($row[$property->getPropertyName()]);

                // Search even when the property does not exist
                if ($input->getOption('find-empty')) {
                    $skip = false;
                }

                // Skip already migrated files
                if (isset($row[$property->getPropertyName()]['filesystems'])) {
                    $skip = true;
                }

                // Skip when it meets the criteria above
                if ($skip) {
                    continue;
                }

                // Fix the one -> many property now foreach and so on
                if ($filename = $property->getFileId($row)) {
                    if ($file = $this->getFile($input->getArgument('path'), $filename, $row['_id'], $input->getOption('ignore-duplicates'))) {
                        // Make a storage object
                        $storage = $this->storage->write(
                            new MemoryReader(
                                $file->getContents(),
                                new Metadata(
                                    $file->getExtension(),
                                    mime_content_type($file->getPathname()),
                                    new ArrayCollection(),
                                    new ArrayCollection()
                                )
                            )
                        );

                        // Convert the property
                        $row[$property->getPropertyName()] = (new StorageTranslation($storage))->toArray();

                        // Write it down, some where
                        $this->database->saveRow($row);

                        // Check for a delete
                        if ($input->getOption('delete')) {
                            @unlink($file->getPathname());
                        }
                    } else {
                        if (isset($row[$property->getPropertyName()])) {
                            // If the property exists, the only valid count is one, what else?
                            throw new \LogicException(
                                sprintf(
                                    'The file %s was found zero times for document %s and property %s.',
                                    $filename,
                                    $row['_id'],
                                    $property->getPropertyName()
                                )
                            );
                        }
                    }
                }
            }

            // Update the barry progress
            $progress->advance();
        }

        // Release the output
        $progress->finish();
        return 0;
    }

    /**
     * @param string $path
     * @param string $fileId
     * @param string $documentId
     * @param bool   $allowDuplicate
     *
     * @return bool|SplFileInfo
     */
    protected function getFile($path, $fileId, $documentId, $allowDuplicate = false)
    {
        // Make a search for a file
        $finder = Finder::create()
            ->files()
            ->in($path)
            ->name(sprintf('%s*', $fileId));

        if (1 == $finder->count()) {
            // Configure the iterator for the first entry
            $iterator = $finder->getIterator();
            $iterator->rewind();

            /** @var SplFileInfo $file */
            $file = clone $iterator->current();

            // Memory optimalization
            unset($iterator);
            unset($finder);

            // Return
            return $file;
        }

        if (1 < $finder->count()) {
            if ($allowDuplicate) {
                // Sort
                $finder->sortByModifiedTime();

                // Configure the iterator for the first entry
                $iterator = $finder->getIterator();
                $iterator->rewind();

                return $iterator->current();
            }

            // This can not be done
            throw new \LogicException(sprintf(
                'The file %s (for document: %s) has been found %d times on the given path.',
                $fileId,
                $documentId,
                $finder->count()
            ));
        }

        return false;
    }
}
