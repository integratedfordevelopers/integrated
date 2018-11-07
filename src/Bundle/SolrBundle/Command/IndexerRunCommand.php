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
use Integrated\Bundle\SolrBundle\EventListener\DoctrineClearEventSubscriber;
use Integrated\Bundle\SolrBundle\Process\ArgumentProcess;
use Integrated\Bundle\SolrBundle\Process\ProcessPoolGenerator;
use Integrated\Common\Queue\Provider\DBAL\QueueProvider;
use Integrated\Common\Solr\Indexer\Indexer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Lock\Factory;
use Symfony\Component\Process\Process;

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
     * @var Factory
     */
    protected $lockFactory;

    /**
     * @var string
     */
    protected $workingDirectory;

    /**
     * @var DoctrineClearEventSubscriber
     */
    protected $clearEventSubscriber;

    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * IndexerRunCommand constructor.
     *
     * @param Indexer                      $indexer
     * @param QueueProvider                $queueProvider
     * @param Factory                      $lockFactory
     * @param DoctrineClearEventSubscriber $clearEventSubscriber
     * @param KernelInterface              $kernel
     * @param string                       $workingDirectory
     */
    public function __construct(
        Indexer $indexer,
        QueueProvider $queueProvider,
        Factory $lockFactory,
        DoctrineClearEventSubscriber $clearEventSubscriber,
        KernelInterface $kernel,
        $workingDirectory
    ) {
        parent::__construct();

        $this->indexer = $indexer;
        $this->queueProvider = $queueProvider;
        $this->lockFactory = $lockFactory;
        $this->workingDirectory = $workingDirectory;
        $this->clearEventSubscriber = $clearEventSubscriber;
        $this->kernel = $kernel;
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
                'Block the current command until all sub-processes are done'
            )
            ->setDescription('Execute a solr indexer run')
            ->setHelp('
The <info>%command.name%</info> command starts a indexer run.

<info>php %command.full_name%</info>
');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($argument = $input->getArgument('processes')) {
            return $this->runProcess(new ArgumentProcess($argument), $input, $output);
        } elseif ($input->getOption('full') || $input->getOption('daemon')) {
            return $this->runExternal($input, $output);
        }

        return $this->runInternal(self::class, $output);
    }

    /**
     * @param string          $lock
     * @param OutputInterface $output
     *
     * @return int
     */
    private function runInternal($lock, OutputInterface $output)
    {
        try {
            $lock = $this->lockFactory->createLock(self::class.md5(__DIR__ . $lock));
            $lock->acquire(true);

            try {
                if ($output->isDebug() && method_exists($this->indexer, 'setDebug')) {
                    $this->indexer->setDebug();
                }

                $this->indexer->execute();
            } finally {
                $lock->release();
            }
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
            // Run a external process
            $process = new Process(
                sprintf('php bin/console solr:indexer:run -e %s', $this->kernel->getEnvironment()),
                $this->workingDirectory
            );

            $process->setTimeout(0);
            $process->run(function ($type, $buffer) use ($output) {
                $output->write($buffer, false, $type);
            });

            if (!$process->isSuccessful()) {
                break; // terminate when there is a error
            }

            if (!$input->getOption('daemon')) {
                if (!$this->indexer->getQueue()->count()) {
                    break;
                }
            }

            usleep($wait);
        }

        return 0;
    }

    /**
     * @param ArgumentProcess $argument
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    private function runProcess(ArgumentProcess $argument, InputInterface $input, OutputInterface $output)
    {
        if ($argument->isParentProcess()) {
            // Create pool generator to generate the processes
            $generator = new ProcessPoolGenerator($input, $this->kernel);
            $pool = $generator->getProcessesPool($argument, $this->workingDirectory);

            // Start them accordingly
            foreach ($pool as $i => $process) {
                // Run it
                $process->start();

                // Tell somebody
                $output->writeln(sprintf('Started process %d with pid %d to run the queue', ($i + 1), $process->getPid()));
            }

            if ($input->getOption('blocking')) {
                $output->writeln('Running in blocking mode, waiting until all started processes are done');

                // While the pool contains processes we're running
                while ($pool->count()) {
                    foreach ($pool as $i => $process) {
                        // Read stout for anything to pass thru
                        if ($processOutput = $process->getIncrementalOutput()) {
                            $output->writeln(sprintf('Prcocess %d: %s', $i, $processOutput));
                        }
                        // Read sterr for anything to pass thru
                        if ($processOutput = $process->getIncrementalErrorOutput()) {
                            $output->writeln(sprintf('Prcocess %d: %s', $i, $processOutput));
                        }

                        if (!$process->isRunning()) {
                            // Tell the user
                            $output->writeln(sprintf('Process %d finished', ($i + 1)));

                            // This one is important
                            $pool->removeElement($process);
                        }
                    }

                    // Don't create a cpu load, check periodically
                    sleep(1);
                }
            }
        } else {
            // Set the modulo to run over the data set with x processes, creating a unique list per thread
            $this->queueProvider->setOption('where', sprintf('(id %% %d) = %d', $argument->getProcessMax(), $argument->getProcessNumber()));

            // Add the clear event listener only for the thread
            $this->indexer->getEventDispatcher()->addSubscriber($this->clearEventSubscriber);

            // Seems to be a sub-process, ran it with a the number appended to the class
            while ($this->indexer->getQueue()->count()) {
                $this->runInternal(sprintf('%s:%d', self::class, $argument->getProcessNumber()), $output);

                // Give them cores some relaxation
                usleep(5000);
            }
        }

        // Good to go
        return 0;
    }
}
