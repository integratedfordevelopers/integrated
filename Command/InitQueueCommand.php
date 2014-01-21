<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SolrBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\DBAL\Schema\SchemaException;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class InitQueueCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('init:queue')
            ->setDescription('Mounts queue tables in the database')
            ->setHelp(<<<EOF
The <info>%command.name%</info> command mounts ACL tables in the database.

<info>php %command.full_name%</info>
EOF
            )
        ;
    }

    /**
     * @see Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $connection = $container->get('integrated_queue.dbal.connection');
        $schema = $container->get('integrated_queue.dbal.schema');

        try {
            $schema->addToSchema($connection->getSchemaManager()->createSchema());
        } catch (SchemaException $e) {
            $output->writeln("Aborting: ".$e->getMessage());

            return 1;
        }

        foreach ($schema->toSql($connection->getDatabasePlatform()) as $sql) {
            $connection->exec($sql);
        }

        $output->writeln('Queue tables have been initialized successfully.');
    }
}
