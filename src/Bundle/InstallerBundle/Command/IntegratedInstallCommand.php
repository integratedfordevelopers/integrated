<?php
namespace Integrated\Bundle\InstallerBundle\Command;

use Doctrine\ORM\EntityManager;
use Integrated\Bundle\InstallerBundle\Install\Migrations;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * Command for executing single migrations up or down manually
 */
class IntegratedInstallCommand extends Command
{
    /**
     * @var Migrations
     */
    private $migrations;
    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * @param EntityManager $manager
     * @param Migrations $migrations
     */
    public function __construct(EntityManager $manager, Migrations $migrations)
    {
        $this->migrations = $migrations;
        $this->manager = $manager;

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
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $steps = $input->getOption('step');

        if (in_array('tests', $steps) || empty($steps)) {
            $output->writeln('Test environment');

            $this->manager->getConnection()->connect();
            if ($this->manager->getConnection()->isConnected()) {
                $output->writeln('MySQL connection OK', $output::VERBOSITY_VERBOSE);
            } else {
                $output->writeln('MySQL connection not OK');
                return;
            }
        }

        if (in_array('cache', $steps) || empty($steps)) {
            $output->writeln('Clear cache');

            $this->executeCommand("cache:clear", $output);
        }

        if (in_array('assets', $steps) || empty($steps)) {
            $output->writeln('Install assets');

            $this->executeCommand("braincrafted:bootstrap:install", $output);
            $this->executeCommand("sp:bower:install", $output);
            $this->executeCommand("assetic:dump", $output);
            $this->executeCommand("fos:js-routing:dump", $output);
            $this->executeCommand("assets:install", $output);
        }

        if (in_array('migrations', $steps) || empty($steps)) {
            $output->writeln('Execute migrations');

            $this->migrations->execute();
        }
/*
$ php bin/console assetic:dump web
$ php bin/console doctrine:mongodb:schema:update


migratie maken: php bin/console doctrine:schema:update --force
$ php bin/console init:queue --force
$ php bin/console init:locking --force
$ php bin/console init:scope
 */
        //
    }

    /**
     * @param $command
     * @param OutputInterface $output
     */
    protected function executeCommand($command, OutputInterface $output)
    {
        $php = escapeshellarg(self::getPhp(false));
        $console = escapeshellarg('bin/console');

        $output->writeln('Execute '.$php.' '.$console.' '.$command, OutputInterface::VERBOSITY_VERY_VERBOSE);

        $process = new Process($php.' '.$console.' '.$command);

        $process->setTimeout(0);
        $process->run(function ($type, $buffer) use ($output) {
            if (Process::ERR === $type) {
                $output->write($buffer);
            } else {
                $output->write($buffer, false, $output::VERBOSITY_VERBOSE);
            }
        });

        if (!$process->isSuccessful()) {
            $output->writeln('Command '.$command.' failed');
        }
    }

    /**
     * @param bool $includeArgs
     * @return array|false|string|null
     */
    protected static function getPhp($includeArgs = true)
    {
        $phpFinder = new PhpExecutableFinder;
        if (!$phpPath = $phpFinder->find($includeArgs)) {
            throw new \RuntimeException(
                'The php executable could not be found, add it to your PATH environment variable and try again'
            );
        }

        return $phpPath;
    }
}
