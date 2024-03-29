<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WorkflowBundle\Command;

use Symfony\Component\Console\Command\Command;
use Exception;
use Integrated\Common\Queue\QueueInterface;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class WorkerCommand extends Command
{
    use LockableTrait;
    /**
     * @var QueueInterface
     */
    private $queue;

    /**
     * @var string
     */
    private $workingDirectory;

    /**
     * @param QueueInterface $queue
     * @param string         $workingDirectory
     */
    public function __construct(
        QueueInterface $queue,
        string $workingDirectory
    ) {
        parent::__construct();

        $this->queue = $queue;
        $this->workingDirectory = $workingDirectory;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('workflow:worker:run')

            ->addOption('batch', 'b', InputOption::VALUE_REQUIRED, 'The queue batch size to process in one worker run', 10)

            ->setDescription('Process the workflow queue messages')
            ->setHelp('
The <info>%command.name%</info> .

<info>php %command.full_name%</info>
');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->lock(self::class.md5(__DIR__.$this->getName()))) {
            $output->writeln('The command is already running in another process.');

            return 0;
        }

        try {
            foreach ($this->queue->pull($input->getOption('batch')) as $message) {
                $data = (array) $message->getPayload();

                $data['command'] = isset($data['command']) ? $data['command'] : null;
                $data['args'] = isset($data['args']) ? $data['args'] : null;

                if ($data['command']) {
                    switch ($data['command']) {
                        case 'index':
                            $data['args'] = \is_array($data['args']) ? $data['args'] : [$data['args']];
                            $data['args'] = array_filter(array_map('trim', $data['args']));

                            if ($data['args']) {
                                $this->executeCommand($input, $output, 'workflow:index', array_merge(['--ignore'], $data['args']));
                            }
                            break;

                        case 'index-full':
                            $this->executeCommand($input, $output, 'workflow:index', ['--full']);
                            break;

                        default:
                            $output->writeln('Unknow command: '.$data['command']);
                            break;
                    }
                } // ignore empty commands

                $message->delete();
            }
        } catch (Exception $e) {
            $output->writeln('Aborting: '.$e->getMessage());

            return 1;
        } finally {
            $this->release();
        }

        return 0;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param string          $command
     * @param string[]        $arguments
     *
     * @throws Exception
     */
    protected function executeCommand(InputInterface $input, OutputInterface $output, $command, array $arguments = [])
    {
        // run in a different process for isolation like memory issues.
        $process = new Process(
            ['php', 'bin/console', $command, '-e', $input->getOption('env'), implode(' ', $arguments)],
            $this->workingDirectory
        );
        $process->run();

        $process->run(function ($type, $buffer) use ($output) {
            if (Process::ERR === $type) {
                $output->write($buffer);
            } else {
                $output->write($buffer, false, $output::VERBOSITY_VERBOSE);
            }
        });

        if (!$process->isSuccessful()) {
            throw new Exception($process->getErrorOutput());
        }
    }
}
