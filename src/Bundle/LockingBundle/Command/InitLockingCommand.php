<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\LockingBundle\Command;

use Doctrine\DBAL\Connection;
use Integrated\Common\Locks\Provider\DBAL\Schema;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class InitLockingCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('init:locking')
            ->setDescription('Mounts locking tables in the database.')
            ->setDefinition([
                new InputOption(
                    'dump-sql',
                    null,
                    InputOption::VALUE_NONE,
                    'Dumps the generated SQL statements to the screen (does not execute them).'
                ),
                new InputOption(
                    'force',
                    null,
                    InputOption::VALUE_NONE,
                    'Causes the generated SQL statements to be physically executed against your database.'
                ),
            ]);

        $this->setHelp(<<<EOT
The <info>%command.name%</info> command generates the SQL needed to
synchronize the database schema with the Locking tables schema.

<info>%command.name% --dump-sql</info>

Alternatively, you can execute the generated queries:

<info>%command.name% --force</info>

If both options are specified, the queries are output and then executed:

<info>%command.name% --dump-sql --force</info>
EOT
        );
    }

    /**
     * @see Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = $this->getConnection();

        $diff = $this->getSchema()->compare($connection->getSchemaManager()->createSchema());
        $diff = $diff->toSaveSql($connection->getDatabasePlatform());

        if (0 === \count($diff)) {
            $output->writeln('Nothing to update - your database is already in sync with the Locking tables schema.');

            return 0;
        }

        $dump = true === $input->getOption('dump-sql');
        $force = true === $input->getOption('force');

        if ($dump) {
            $output->writeln(implode(';'.PHP_EOL, $diff).';');
        }

        if ($force) {
            if ($dump) {
                $output->writeln('');
            }

            $output->writeln('Updating database schema...');

            foreach ($diff as $sql) {
                $connection->exec($sql);
            }

            $output->writeln(
                sprintf('Database schema updated successfully! "<info>%s</info>" queries were executed', \count($diff))
            );
        }

        if ($dump || $force) {
            return 0;
        }

        $output->writeln(
            '<comment>ATTENTION</comment>: This operation should not be executed in a production environment.'
        );
        $output->writeln('           Use the incremental update to detect changes during development and use');
        $output->writeln('           the SQL DDL provided to manually update your database in production.');
        $output->writeln('');

        $output->writeln(
            sprintf('The Schema-Tool would execute <info>"%s"</info> queries to update the database.', \count($diff))
        );
        $output->writeln('Please run the operation by passing one - or both - of the following options:');

        $output->writeln(sprintf('    <info>%s --force</info> to execute the command', $this->getName()));
        $output->writeln(
            sprintf('    <info>%s --dump-sql</info> to dump the SQL statements to the screen', $this->getName())
        );

        return 1;
    }

    /**
     * @return Connection
     */
    protected function getConnection()
    {
        return $this->getContainer()->get('integrated_locking.dbal.connection');
    }

    /**
     * @return Schema
     */
    protected function getSchema()
    {
        return $this->getContainer()->get('integrated_locking.dbal.schema');
    }
}
