<?php


namespace Integrated\Bundle\InstallerBundle\Command;

use Integrated\Bundle\InstallerBundle\Install\Migrations;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\PhpExecutableFinder;

/**
 * Command for executing single migrations up or down manually
 */
class IntegratedInstallCommand extends Command
{
    protected $migrations;

    /**
     * Migrations constructor.
     */
    public function __construct(Migrations $migrations)
    {
        $this->migrations = $migrations;

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

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $steps = $input->getOption('step');

        if (in_array('cache', $steps) || empty($steps)) {
            $this->executeCommand("cache:clear", $output);
        }

        if (in_array('assets', $steps) || empty($steps)) {
            $output->writeln('Executing assetic installation step', OutputInterface::VERBOSITY_VERBOSE);

            $this->executeCommand("braincrafted:bootstrap:install", $output);
            $this->executeCommand("sp:bower:install:install", $output);
            $this->executeCommand("assetic:dump", $output);
            $this->executeCommand("fos:js-routing:dump", $output);
            $this->executeCommand("assets:install", $output);
        }

        if (in_array('migrations', $steps) || empty($steps)) {
            $output->writeln('Executing migrations installation step', OutputInterface::VERBOSITY_VERBOSE);

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

    protected function executeCommand($command, OutputInterface $output)
    {
        $php = escapeshellarg(self::getPhp(false));
        $console = escapeshellarg('bin/console');

        $process->setTimeout(0);
        $process->run(function ($type, $buffer) use ($output) {
            $output->write($buffer, false, $type);
        });

        $output->writeln('Execute '.$command, OutputInterface::VERBOSITY_VERY_VERBOSE);
        if (!$process->isSuccessful()) {
            $output->writeln('Command '.$command.' failed');
        }
    }

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
