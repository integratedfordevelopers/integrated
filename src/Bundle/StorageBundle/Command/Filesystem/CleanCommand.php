<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Command\Filesystem;

use Integrated\Bundle\StorageBundle\Storage\Filesystem\CleanFilesystem;
use Integrated\Bundle\StorageBundle\Storage\Registry\FilesystemRegistry;
use Integrated\Common\Storage\Database\DatabaseInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Remove unused files from the storage.
 */
class CleanCommand extends Command
{
    /**
     * @var DatabaseInterface
     */
    protected $database;

    /**
     * @var FilesystemRegistry
     */
    protected $registry;

    /**
     * @param DatabaseInterface  $database
     * @param FilesystemRegistry $registry
     */
    public function __construct(
        DatabaseInterface $database,
        FilesystemRegistry $registry
    ) {
        $this->database = $database;
        $this->registry = $registry;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('storage:filesystem:clean')
            ->setDescription('Remove unused files from the storage')
            ->setDefinition([
                new InputArgument(
                    'filesystem',
                    InputArgument::REQUIRED,
                    'Name of the filesystem to clean'
                ),
                new InputArgument(
                    'directory',
                    InputArgument::REQUIRED,
                    'Target directory for movement of the used files'
                ),
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filesystem = $input->getArgument('filesystem');
        $directory = $input->getArgument('directory');

        $cleanFileSystem = new CleanFilesystem($this->registry, $this->database);
        $cleanFileSystem->clean($filesystem, $directory);

        $output->writeln(sprintf('Cleanable files for %s have been moved to %s', $filesystem, $directory));

        return 0;
    }
}
