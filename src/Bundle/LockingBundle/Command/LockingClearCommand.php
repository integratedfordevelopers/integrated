<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\LockingBundle\Command;

use Integrated\Common\Locks\ManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class LockingClearCommand extends Command
{
    /**
     * @var ManagerInterface
     */
    private $manager;

    /**
     * LockingClearCommand constructor.
     *
     * @param ManagerInterface $manager
     */
    public function __construct(ManagerInterface $manager)
    {
        parent::__construct();

        $this->manager = $manager;
    }

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('locking:clear')
            ->setDescription('Clear up all locks')
            ->setHelp(<<<EOF
The <info>%command.name%</info> removes all the locks that are set

<info>php %command.full_name%</info>
EOF
            );
    }

    /**
     * @see Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->manager->clear();

        return 0;
    }
}
