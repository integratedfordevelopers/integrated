<?php

namespace Integrated\Bundle\InstallerBundle\Command;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManager;
use Integrated\Bundle\InstallerBundle\Install\MongoDBMigrations;
use Integrated\Bundle\InstallerBundle\Install\MySQLMigrations;
use Integrated\Bundle\InstallerBundle\Test\BundleTest;
use Solarium\Client;
use Solarium\QueryType\Select\Query\Query;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * Command for executing single migrations up or down manually.
 */
class IntegratedInstallCommand extends Command
{
    /**
     * @var MySQLMigrations
     */
    private $migrations;

    /**
     * @var MongoDBMigrations
     */
    private $mongoDBMigrations;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var DocumentManager
     */
    private $documentManager;

    /**
     * @var Client
     */
    private $solrClient;

    /**
     * @var BundleTest
     */
    private $bundleTest;

    /**
     * @param EntityManager   $entityManager
     * @param DocumentManager $documentManager
     * @param Client          $solrClient
     * @param MySQLMigrations $migrations
     * @param BundleTest      $bundleTest
     */
    public function __construct(EntityManager $entityManager, DocumentManager $documentManager, Client $solrClient, MySQLMigrations $migrations, MongoDBMigrations $mongoDBMigrations, BundleTest $bundleTest)
    {
        $this->migrations = $migrations;
        $this->mongoDBMigrations = $mongoDBMigrations;
        $this->entityManager = $entityManager;
        $this->documentManager = $documentManager;
        $this->solrClient = $solrClient;
        $this->bundleTest = $bundleTest;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('integrated:install')
            ->setDescription('Run the Integrated installer to set up database scheme etc.')
            ->addOption(
                'step',
                's',
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Specify step to run. Choices: migrations. You can add this option multiple times. If not specified all steps will be executed.'
            );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $steps = $input->getOption('step');
        $io = new SymfonyStyle($input, $output);

        if (\in_array('tests', $steps) || empty($steps)) {
            $io->section('Test environment');

            $this->solrClient->execute(new Query());
            $io->success('Solr connection successful');

            $bundleErrors = $this->bundleTest->execute();
            if (\count($bundleErrors) > 0) {
                foreach ($bundleErrors as $bundleError) {
                    $io->error($bundleError);
                }
            } else {
                $io->success('Bundle test successful');
            }
        }

        if (\in_array('cache', $steps) || empty($steps)) {
            $io->section('Clear cache');

            $this->executeCommand('cache:clear', $output);
        }

        if (\in_array('assets', $steps) || empty($steps)) {
            $io->section('Install assets');

            $this->executeCommand('assets:install', $output);
        }

        if (\in_array('migrations', $steps) || empty($steps)) {
            $io->section('Execute migrations');

            $this->migrations->execute();
            $this->mongoDBMigrations->execute();
        }

        return 0;
    }

    /**
     * @param $command
     * @param OutputInterface $output
     */
    protected function executeCommand($command, OutputInterface $output)
    {
        $php = self::getPhp(false);
        $console = 'bin/console';

        $output->writeln(sprintf('Execute %s %s %s', $php, $console, $command), OutputInterface::VERBOSITY_VERY_VERBOSE);

        $process = new Process([$php, $console, $command]);

        $process->setTimeout(0);
        $process->run(function ($type, $buffer) use ($output) {
            if (Process::ERR === $type) {
                $output->write($buffer);
            } else {
                $output->write($buffer, false, $output::VERBOSITY_VERBOSE);
            }
        });

        if (!$process->isSuccessful()) {
            $output->writeln(sprintf('Command %s failed', $command));
        }
    }

    /**
     * @param bool $includeArgs
     *
     * @return array|false|string|null
     */
    protected static function getPhp($includeArgs = true)
    {
        $phpFinder = new PhpExecutableFinder();
        if (!$phpPath = $phpFinder->find($includeArgs)) {
            throw new \RuntimeException(
                'The php executable could not be found, add it to your PATH environment variable and try again'
            );
        }

        return $phpPath;
    }
}
