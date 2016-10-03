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
use Integrated\Bundle\StorageBundle\Storage\Collection\Walk\DocumentWalk;
use Integrated\Bundle\StorageBundle\Storage\Collection\Walk\FilesystemWalk;
use Integrated\Bundle\StorageBundle\Storage\Reflection\Cache\ObjectCache;
use Integrated\Bundle\StorageBundle\Storage\Reflection\PropertyReflection;
use Integrated\Bundle\StorageBundle\Storage\Util\ProgressIteratorUtil;
use Integrated\Bundle\StorageBundle\Storage\Registry\FilesystemRegistry;

use Integrated\Common\Storage\Database\DatabaseInterface;
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
     * @var PropertyReflection[]
     */
    protected $reflectionClasses;

    /**
     * @param DatabaseInterface $database
     * @param FilesystemRegistry $registry
     * @param ManagerInterface $storage
     */
    public function __construct(DatabaseInterface $database, FilesystemRegistry $registry, ManagerInterface $storage)
    {
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
        $this->setName('storage:filesystem:add')
            ->setDescription('Add files into the filesystem.')
            ->setDefinition([
                new InputArgument(
                    'filesystem',
                    InputArgument::REQUIRED,
                    ''
                )
            ])
        ;
    }

    /**
     * {@inheritdoc}
     * @throws \InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $reflection = new ObjectCache();
        $iteratorUtil = new ProgressIteratorUtil($this->database->getObjects(), $output);

        $output->writeln('Running three steps; map, store and save');

        $iteratorUtil
            ->map(ContentReflectionMap::storageProperties($reflection))
            ->walk(FilesystemWalk::add($this->storage, $reflection, $input->getArgument('filesystem')))
            ->walk(DocumentWalk::save($this->database))
        ;
    }
}
