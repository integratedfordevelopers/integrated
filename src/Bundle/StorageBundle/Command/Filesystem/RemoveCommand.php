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

use Integrated\Bundle\StorageBundle\Storage\Collection\Map\ContentReflectionMap;
use Integrated\Bundle\StorageBundle\Storage\Collection\Map\FileMap;
use Integrated\Bundle\StorageBundle\Storage\Collection\Walk\DocumentWalk;
use Integrated\Bundle\StorageBundle\Storage\Collection\Walk\FilesystemWalk;
use Integrated\Bundle\StorageBundle\Storage\Mapping\MetadataFactoryInterface;
use Integrated\Bundle\StorageBundle\Storage\Registry\FilesystemRegistry;
use Integrated\Bundle\StorageBundle\Storage\Util\ProgressIteratorUtil;
use Integrated\Common\Storage\Database\DatabaseInterface;
use Integrated\Common\Storage\ManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Deletes all files in the database of the filesystem.
 *
 * @author Johnny Borg <johnny@e-active.nl>
 */
class RemoveCommand extends Command
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
     * @var ManagerInterface
     */
    protected $storage;

    /**
     * @var MetadataFactoryInterface
     */
    private $metadata;

    /**
     * @param DatabaseInterface  $database
     * @param FilesystemRegistry $registry
     * @param ManagerInterface   $storage
     */
    public function __construct(
        DatabaseInterface $database,
        FilesystemRegistry $registry,
        ManagerInterface $storage
    ) {
        $this->database = $database;
        $this->registry = $registry;
        $this->storage = $storage;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('storage:filesystem:remove')
            ->setDescription('Removes the filesystem from the database and copies the files to the other filesystem.')
            ->setDefinition([
                new InputArgument(
                    'filesystem',
                    InputArgument::REQUIRED,
                    ''
                ),
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filesystem = $input->getArgument('filesystem');

        if ($this->registry->exists($filesystem)) {
            // This we'll need to do some work
            $iteratorUtil = new ProgressIteratorUtil($this->database->getObjects(), $output);

            $output->writeln('Running three steps; map, deleting files and save');

            $iteratorUtil
                ->map(ContentReflectionMap::storageProperties($this->metadata))
                ->map(FileMap::documentFilesystemContains($this->metadata, $filesystem))
                ->walk(FilesystemWalk::remove($this->storage, $this->metadata, $filesystem))
                ->walk(DocumentWalk::save($this->database))
            ;
        } else {
            throw new \InvalidArgumentException(sprintf('The filesystem %s does not exist', $filesystem));
        }

        return 0;
    }
}
