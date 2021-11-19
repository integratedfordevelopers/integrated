<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ChannelBundle\Command;

use Symfony\Component\Console\Command\Command;
use Exception;
use Integrated\Common\Channel\Exporter\QueueExporter;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Process;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ExportCommand extends Command
{
    /**
     * @var QueueExporter
     */
    private $exporter;

    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * @var string
     */
    protected $workingDirectory;

    /**
     * Constructor.
     *
     * @param QueueExporter $exporter
     */
    public function __construct(
        QueueExporter $exporter,
        KernelInterface $kernel,
        $workingDirectory
    ) {
        $this->exporter = $exporter;
        $this->workingDirectory = $workingDirectory;
        $this->kernel = $kernel;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('channel:export')

            ->addOption('full', 'f', InputOption::VALUE_NONE, 'Keep running until the queue is empty')
            ->addOption(
                'daemon',
                'd',
                InputOption::VALUE_NONE,
                'Keep running until the programme is manually closed, this option overwrites --full'
            )
            ->addOption(
                'wait',
                'w',
                InputOption::VALUE_REQUIRED,
                'Time in milliseconds to wait between runs (in combination with --full or --daemon)',
                0
            )

            ->setDescription('Execute a channel exporter run');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->getOption('full') || $input->getOption('daemon')) {
            return $this->runExternal($input, $output);
        }

        return $this->runInternal($input, $output);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    private function runInternal(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->exporter->execute();
        } catch (Exception $e) {
            $output->writeln('Aborting: '.$e->getMessage());

            return 1;
        }

        return 0;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    private function runExternal(InputInterface $input, OutputInterface $output)
    {
        $wait = (int) $input->getOption('wait');
        $wait = $wait * 1000; // convert from milli to micro

        while (true) {
            $process = new Process(
                ['php', 'bin/console', 'channel:export', '-e', $this->kernel->getEnvironment()],
                $this->workingDirectory,
                null,
                null,
                null
            );
            $process->run(function ($type, $buffer) use ($output) {
                $output->write($buffer, false, OutputInterface::OUTPUT_RAW);
            });

            if (!$process->isSuccessful()) {
                break; // terminate when there is a error
            }

            if (!$input->getOption('daemon')) {
                if (!$this->exporter->getQueue()->count()) {
                    break;
                }
            }

            usleep($wait);
        }

        return 0;
    }
}
