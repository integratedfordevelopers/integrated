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
use Integrated\Bundle\StorageBundle\Storage\Resolver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Lists the configured filesystem(s).
 *
 * @author Johnny Borg <johnny@e-active.nl>
 */
class ListCommand extends Command
{
    /**
     * @var FilesystemRegistry
     */
    protected $registry;

    /**
     * @var Resolver
     */
    protected $resolverStorage;

    /**
     * @param FilesystemRegistry $registry
     * @param Resolver           $resolverStorage
     */
    public function __construct(FilesystemRegistry $registry, Resolver $resolverStorage)
    {
        $this->registry = $registry;
        $this->resolverStorage = $resolverStorage;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('storage:list')
            ->setDescription('Lists the configured filesystem(s).')
            ->setHelp('The <info>%command.name%</info> lists the existing filesystem(s).');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->registry->getIterator() as $key => $filesystem) {
            $output->writeln(
                sprintf(
                    '<info>%s</info>: %s',
                    $key,
                    \get_class($filesystem->getAdapter())
                )
            );

            if ($options = $this->resolverStorage->getOptions($key)) {
                $output->writeln(
                    [
                        sprintf(
                            "\t resolver_class: %s",
                            $options['resolver_class']
                        ),
                        sprintf(
                            "\t public: %s",
                            $options['public']
                        ),
                    ]
                );
            }
        }

        return 0;
    }
}
