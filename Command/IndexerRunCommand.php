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

use Exception;

use Integrated\Bundle\SolrBundle\Process\ArgumentProcess;
use Integrated\Bundle\SolrBundle\Process\ProcessPoolGenerator;

use Integrated\Common\Queue\Provider\DBAL\QueueProvider;
use Integrated\Common\Queue\Queue;
use Integrated\Common\Solr\Indexer\Indexer;
use Integrated\Common\Solr\Indexer\IndexerInterface;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Filesystem\LockHandler;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class IndexerRunCommand extends Command
{
    /**
     * @var Indexer
     */
    protected $indexer;

    /**
     * @var QueueProvider
     */
    protected $queueProvider;

    /**
     * @var string
     */
    protected $workingDirectory;

    /**
     * @param Indexer $indexer
     * @param Queue $solrQueue
     * @param string $workingDirectory
     */
    public function __construct(Indexer $indexer, QueueProvider $queueProvider, $workingDirectory)
    {
        parent::__construct();

        $this->indexer = $indexer;
        $this->queueProvider = $queueProvider;
        $this->workingDirectory = $workingDirectory;
    }

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('solr:indexer:run')
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
            ->addArgument(
                'processes',
                InputArgument::OPTIONAL,
                'Creates a number of proccess that run the queue',
                0
            )
            ->addOption(
                'blocking',
                'b',
                InputOption::VALUE_NONE,
                0
            )
            ->setDescription('Execute a sol indexer run')
            ->setHelp('
The <info>%command.name%</info> command starts a indexer run.

<info>php %command.full_name%</info>
');

    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($argument = $input->getArgument('processes')) {
            return $this->runProcess(new ArgumentProcess($argument), $input, $output);
        } else if ($input->getOption('full') || $input->getOption('daemon')) {
            return $this->runExternal($input);
        }

        return $this->runInternal(self::class, $output);
    }

    /**
     * @param string $lock
     * @param OutputInterface $output
     * @return int
     */
    private function runInternal($lock, OutputInterface $output)
    {
        try {
            $lock = new LockHandler($lock);
            $attempts = 0;
            while (!$lock->lock()) {
                // Retry for almost a minute, otherwise don't throw an error (after all another indexer is running)
                if ($attempts++ >= 10) {
                    return 0;
                }
                sleep(5);
            }

            if ($output->isDebug() && method_exists($this->indexer, 'setDebug')) {
                $this->indexer->setDebug();
            }

            $this->indexer->execute();

        } catch (Exception $e) {
            $output->writeln("Aborting: " . $e->getMessage());

            return 1;
        }

        return 0;
    }

    /**
     * @param InputInterface $input
     * @return int
     */
    private function runExternal(InputInterface $input)
    {
        $wait = (int) $input->getOption('wait');
        $wait = $wait * 1000; // convert from milli to micro

        while (true) {
            // Run a external process
            $process = new Process('php app/console solr:indexer:run', $this->workingDirectory);

            $process->setTimeout(0);
            $process->run(function ($type, $buffer) use ($output) {
                $output->write($buffer, false, $type);
            });

            if (!$process->isSuccessful()) {
                break; // terminate when there is a error
            }

            if (!$input->getOption('daemon')) {
                if ($this->indexer->getQueue()->count()) {
                    break;
                }
            }

            usleep($wait);
        }

        return 0;
    }

    /**
     * @param ArgumentProcess $argumentProcess
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    private function runProcess(ArgumentProcess $argument, InputInterface $input, OutputInterface $output)
    {
        if ($argument->isParentProcess()) {
            // Create pool generator to generate the processes
            $generator = new ProcessPoolGenerator($input);
            $pool = $generator->getProcessesPool($argument, $this->workingDirectory);

            // Start them accordingly
            foreach ($pool as $i => $process) {
                // Run it
                $process->start();

                // Tell somebody
                $output->writeln(sprintf('Started process %d with pid %d to run the queue.', ($i+1), $process->getPid()));
            }

            if ($input->getOption('blocking')) {
                $output->writeln('Running in blocking mode, waiting until all started process are done');

                // While the pool contains processes we're running
                while ($pool->count()) {
                    foreach ($pool as $i => $process) {
                        if (!$process->isRunning()) {
                            $output->writeln(sprintf('Process %d finnished.', ($i+1)));

                            // This one is important
                            $pool->removeElement($process);
                        }
                    }

                    // Don't create a cpu load, check periodically
                    sleep(1);
                }
            }

            // Good to go
            return 0;
        }

        // Set the modulo to run over the dataset with x processes
        $this->queueProvider->setOption('where', sprintf('(id %% %d) = %d', $argument->getProcessMax(), $argument->getProcessNumber()));

        // Remove the limit so we'll keep on johnny walk'n
        $this->indexer->setOption('queue.size', -1);

        // Seems to be a sub-process, ran it with a the number appended to the class
        return $this->runInternal(sprintf('%s:%d', self::class, $argument->getProcessNumber()), $output);
    }
}
