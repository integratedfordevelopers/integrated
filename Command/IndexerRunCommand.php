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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Filesystem\LockHandler;

use Exception;

use Integrated\Common\Solr\Indexer\IndexerInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class IndexerRunCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('solr:indexer:run')
            ->addOption('full', 'f', InputOption::VALUE_NONE, 'Keep running until the queue is empty')
            ->addOption('daemon', 'd', InputOption::VALUE_NONE, 'Keep running until the programme is manually closed, this option overwrites --full')
            ->addOption('wait', 'w', InputOption::VALUE_REQUIRED, 'Time in milliseconds to wait between runs (in combination with --full or --daemon)', 0)
            ->setDescription('Execute a sol indexer run')
            ->setHelp('
The <info>%command.name%</info> command starts a indexer run.

<info>php %command.full_name%</info>
'
            );
    }

    /**
     * @see Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('full') || $input->getOption('daemon')) {
            return $this->runExternal($input, $output);
        }

        return $this->runInternal($input, $output);
    }

    private function runInternal(InputInterface $input, OutputInterface $output)
    {
        try {

            $lock = new LockHandler('Integrated\Bundle\SolrBundle\Command\IndexerRunCommand');
            $attemps = 0;
            while (!$lock->lock()) {
                //retry for almost a minute, otherwise don't throw an error (after all another indexer is running)
                if ($attemps++ >= 10) {
                    return 0;
                }
                sleep(5);
            }

            /** @var IndexerInterface $indexer */
            $indexer = $this->getContainer()->get('integrated_solr.indexer');
            $indexer->execute();

        } catch (Exception $e) {
            $output->writeln("Aborting: " . $e->getMessage());

            return 1;
        }

        return 0;
    }

    private function runExternal(InputInterface $input, OutputInterface $output)
    {
        $wait = (int)$input->getOption('wait');
        $wait = $wait * 1000; // convert from milli to micro

        while (true) {
            $process = new Process('php app/console solr:indexer:run -e ' . $input->getOption('env'), getcwd(), null, null, null);
            $process->run();

            if (!$process->isSuccessful()) {
                break; // terminate when there is a error
            }

            if (!$input->getOption('daemon')) {
                if (!$this->getContainer()->get('integrated_solr.indexer')->getQueue()->count()) {
                    break;
                }
            }

            usleep($wait);
        }

        return 0;
    }
}
