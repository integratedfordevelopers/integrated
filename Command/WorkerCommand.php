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
use Symfony\Component\Filesystem\LockHandler;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class WorkerCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('solr:worker:run')

            ->addOption('tasks', 't', InputOption::VALUE_REQUIRED, 'The maximum number of tasks to execute in one worker run', null)

            ->setDescription('Execute worker task from the queue.')
            ->setHelp('
The <info>%command.name%</info> command starts a solr worker run.

<info>php %command.full_name%</info>
');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $lock = new LockHandler(self::class);

        if (!$lock->lock()) {
            return;
        }

        try {
            $worker = $this->getContainer()->get('integrated_solr.worker');

            if (null !== ($tasks = $input->getOption('tasks'))) {
                $worker->setOption('tasks', intval($tasks));
            }

            $worker->execute();
        } finally {
            $lock->release();
        }
    }
}
