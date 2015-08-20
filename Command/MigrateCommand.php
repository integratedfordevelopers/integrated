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

use Integrated\Bundle\StorageBundle\Document\Embedded\Metadata;
use Integrated\Bundle\StorageBundle\Storage\Database\DatabaseInterface;
use Integrated\Bundle\StorageBundle\Storage\Database\Translation\StorageTranslation;
use Integrated\Bundle\StorageBundle\Storage\Manager;
use Integrated\Bundle\StorageBundle\Storage\Reader\MemoryReader;
use Integrated\Bundle\StorageBundle\Storage\Reflection\StorageReflection;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class MigrateCommand extends Command
{
    /**
     * @const Content type class
     */
    const ClassName = 'Integrated\\Bundle\\StorageBundle\\Document\\File';

    /**
     * @var DatabaseInterface
     */
    protected $database;

    /**
     * @var Manager
     */
    protected $storage;

    /**
     * @var array
     */
    protected $reflectionClasses = [];

    /**
     * @param DatabaseInterface $database
     * @param Manager $storage
     */
    public function __construct(DatabaseInterface $database, Manager $storage)
    {
        $this->database = $database;
        $this->storage = $storage;

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
                    InputOption::VALUE_OPTIONAL,
                    'Delete the local file after placing it in the storage'
                ),
                new InputOption(
                    'class',
                    'c',
                    InputOption::VALUE_OPTIONAL,
                    'The class to convert, additionally a space separated list may be given.'
                )
            ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (null == $input->getOption('class')) {
            $classes = [
                'Integrated\\Bundle\\ContentBundle\\Document\\Content\\Image',
                'Integrated\\Bundle\\ContentBundle\\Document\\Content\\File',
            ];
        } else {
            $classes = explode(' ', $input->getOption('class'));
        }

        $data = $this->database->getRows();

        // Barry progress
        $progress = new ProgressBar($output, count($data));
        $progress->start();
        $progress->setFormat(' %current%/%max% [%bar%] %percent:3s%% %remaining:-6s%');
        $current = 0;

        foreach ($data as $row) {
            // Change the relation mapping (if exists) for Integrated
            if (isset($row['relations'])) {
                foreach ($row['relations'] as $n => $relation) {
                    foreach ($relation['references'] as $e => $reference) {
                        if (in_array($reference['class'], $classes)) {
                            $row['relations'][$n]['references'][$e]['class'] = self::ClassName;
                        }
                    }
                }
            }
            // Walk over (embedded relations) keys
            foreach ($row as $key => $value) {
                if (is_array($value)) {
                    if (isset($row[$key]['$ref']) && isset($row[$key]['$id']) && isset($row[$key]['class'])) {
                        if (in_array($row[$key]['class'], $classes)) {
                            $row[$key]['class'] = self::ClassName;
                        }
                    }
                }
            }

            // Only perform the action for the listed classes
            if (in_array($row['class'], $classes)) {
                // Modify the class name for the ORM
                $row['class'] = self::ClassName;
            }

            foreach ($this->getReflectionClass($row['class'])->getStorageProperties() as $property) {
                // Fix the one -> many property now foreach and so on
                if ($filename = $property->getFileId($row)) {
                    if ($file = $this->getFile($input->getArgument('path'), $filename, $row['_id'])) {

                        // Make a storage object
                        $storage = $this->storage->write(
                            new MemoryReader(
                                $file->getContents(),
                                new Metadata(
                                    $file->getExtension(),
                                    mime_content_type($file->getPathname())
                                )
                            )
                        );

                        $row[$property->getPropertyName()] = (new StorageTranslation($storage))->toArray();

                        // Check for a delete
                        if ($input->getOption('delete')) {
                            @unlink($file->getPathname());
                        }
                    } else {
                        var_dump($property);
                        var_dump($row);

                        // The only valid count is one, what else?
                        throw new \LogicException(
                            sprintf(
                                'The file %s was found zero times, the ID must be unique in the given path and exist.',
                                $filename
                            )
                        );
                    }
                }
            }

            // Write it down, some where
            $this->database->saveRow($row);

            // Update the barry progress
            $progress->setProgress(++$current);
        }

        // Release the output
        $progress->finish();

        // Update the content types
        foreach ($classes as $class) {
            // Change the content type
            $this->database->updateContentType(
                $class,
                self::ClassName
            );
        }
    }

    /**
     * @param $class
     * @return StorageReflection
     */
    protected function getReflectionClass($class)
    {
        if (isset($this->reflectionClasses[$class])) {
            return $this->reflectionClasses[$class];
        }

        return $this->reflectionClasses[$class] = new StorageReflection($class);
    }

    /**
     * @param $path
     * @param $fileId
     * @param $documentId
     * @return bool|\Symfony\Component\Finder\SplFileInfo
     */
    protected function getFile($path, $fileId, $documentId)
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
            return $iterator->current();
        } elseif (1 < $finder->count()) {
            // This can not be done
            throw new \LogicException(
                sprintf(
                    'The file %s (for document: %s) has been found %d times on the given path.',
                    $fileId,
                    $documentId,
                    $finder->count()
                )
            );
        }

        return false;
    }
}
