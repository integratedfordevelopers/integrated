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

use Integrated\Bundle\StorageBundle\Storage\Registry\FilesystemRegistry;
use Integrated\Bundle\StorageBundle\Storage\Validation\FilesystemValidation;
use Integrated\Common\Storage\Database\DatabaseInterface;
use Integrated\Common\Storage\ManagerInterface;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Deletes all files in the database of the filesystem.
 *
 * @author Johnny Borg <johnny@e-active.nl>
 */
class ClearCommand extends Command
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
        $this->setName('storage:filesystem:clear')
            ->setDescription('Deletes all files in the database of the filesystem.')
            ->setDefinition([
                new InputArgument(
                    'filesystems',
                    InputArgument::IS_ARRAY,
                    'A space separated list of storage keys'
                ),
                new InputOption(
                    'delete',
                    null,
                    InputOption::VALUE_OPTIONAL,
                    'Delete the file before putting it in storage(s)'
                )
            ]);
    }

    /**
     * {@inheritdoc}
     * @throws \InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $availableFilesystems = (new FilesystemValidation($this->registry))
            ->getValidFilesystems($input->getArgument('filesystems'));

        foreach ($this->database->getFiles() as $i => $file) {
            $validFilesystems = new ArrayCollection();

            foreach ($availableFilesystems as $filesystem) {
                if ($file->getFile()->getFilesystems()->contains($filesystem)) {
                    $validFilesystems[] = $filesystem;
                }
            }

            if (count($validFilesystems)) {
                // Remove the obsolete filesystems
                $file->setFile(
                    $this->storage->delete($file->getFile(), $validFilesystems)
                );
                $this->database->save($file);
            }

            if (0 == ($i % 100)) {
                $this->database->commit();
            }
        }

        // Any left overs from the party
        $this->database->commit();
    }
}
