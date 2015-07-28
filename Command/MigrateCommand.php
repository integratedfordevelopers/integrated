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
use Integrated\Bundle\StorageBundle\Storage\Registry\FilesystemRegistry;

use Integrated\Bundle\StorageBundle\Storage\Validation\FilesystemValidation;
use Symfony\Component\Console\Command\Command;
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
                    'path', InputArgument::REQUIRED,
                    'A local path where (might have a directroy structure) the files can be resolved'
                ),
                new InputOption(
                    'delete', 'd', InputOption::VALUE_OPTIONAL,
                    'Delete the local file after placing it in the storage'
                ),
                new InputOption(
                    'class', 'c', InputOption::VALUE_OPTIONAL,
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

        // Iterate over the rows in the database
        foreach ($classes as $class) {
            // Output the current class
            $output->write(
                sprintf('<info>%s</info> started on migration.', $class),
                true
            );

            foreach ($this->database->getRows($class) as $row) {
                // Make a search for a file
                $finder = Finder::create()
                    ->files()
                    ->in($input->getArgument('path'))
                    ->name(sprintf('%s*', $row['_id']));

                if (1 == $finder->count()) {
                    // Configure the iterator for the first entry
                    $iterator = $finder->getIterator();
                    $iterator->rewind();
                    /** @var \Symfony\Component\Finder\SplFileInfo $file */
                    $file = $iterator->current();

                    // Make an storage object
                    $storage = $this->storage->write(
                        new MemoryReader(
                            $file->getContents(),
                            new Metadata(
                                $file->getExtension(),
                                mime_content_type($file->getPathname())
                            )
                        )
                    );

                    // Assign new stuff
                    $row['file'] = (new StorageTranslation($storage))->toArray();
                    //$row['class'] = self::ClassName;

                    // Write it down, some where
                    $this->database->saveRow($row);

                    // Check for a delete
                    if ($input->getOption('delete')) {
                        @unlink($file->getPathname());
                    }
                } else {
                    // The only valid count is one, what else?
                    throw new \LogicException(
                        sprintf(
                            'The file %s was found %d times, the ID must be unique in the given path and exist.',
                            $row['_id'],
                            $finder->count()
                        )
                    );
                }
            }

            // Change the content type
            $this->database->updateContentType(
                $class,
                self::ClassName
            );
        }
    }
}
