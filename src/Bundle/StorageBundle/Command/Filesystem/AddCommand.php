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
use Integrated\Common\Storage\DecisionInterface;
use Integrated\Common\Storage\ManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Redistribute files in the database over the filesystems.
 *
 * @author Johnny Borg <johnny@e-active.nl>
 */
class AddCommand extends Command
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
     * @var DecisionInterface
     */
    private $decision;

    /**
     * @var MetadataFactoryInterface
     */
    private $metadata;

    /**
     * @param DatabaseInterface        $database
     * @param FilesystemRegistry       $registry
     * @param ManagerInterface         $storage
     * @param DecisionInterface        $decision
     * @param MetadataFactoryInterface $metadata
     */
    public function __construct(
        DatabaseInterface $database,
        FilesystemRegistry $registry,
        ManagerInterface $storage,
        DecisionInterface $decision,
        MetadataFactoryInterface $metadata
    ) {
        $this->database = $database;
        $this->registry = $registry;
        $this->storage = $storage;
        $this->decision = $decision;
        $this->metadata = $metadata;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('storage:filesystem:add')
            ->setDescription('Add files into the filesystem.')
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

            $output->writeln('Running four steps; fetch, check for adding, writing file and save database');

            $iteratorUtil
                ->map(ContentReflectionMap::storageProperties($this->metadata))
                ->map(FileMap::documentAllowed($this->decision, $filesystem))
                ->walk(FilesystemWalk::add($this->storage, $this->metadata, $filesystem))
                ->walk(DocumentWalk::save($this->database))
            ;
        } else {
            throw new \InvalidArgumentException(sprintf('The filesystem %s does not exist', $filesystem));
        }
        return 0;
    }
}
