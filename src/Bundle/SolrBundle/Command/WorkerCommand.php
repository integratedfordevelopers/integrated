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

use Symfony\Component\Console\Command\Command;
use Integrated\Common\Solr\Task\Worker;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Lock\Factory;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class WorkerCommand extends Command
{
    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var Worker
     */
    private $worker;

    /**
     * @param Worker  $worker
     * @param Factory $factory
     */
    public function __construct(Worker $worker, Factory $factory)
    {
        parent::__construct();

        $this->worker = $worker;
        $this->factory = $factory;
    }

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
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $lock = $this->factory->createLock(self::class.md5(__DIR__));

        if (!$lock->acquire()) {
            return 0;
        }

        try {
            if (null !== ($tasks = $input->getOption('tasks'))) {
                $this->worker->setOption('tasks', (int) $tasks);
            }

            $this->worker->execute();
        } finally {
            $lock->release();
        }

        return 0;
    }
}
