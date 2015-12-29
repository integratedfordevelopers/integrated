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

use Integrated\Common\Locks\Provider\DBAL\Manager;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class LockingDBALCleanUpCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('locking:dbal:clean')
            ->setDescription('Clean up the expired locks')
            ->setHelp(<<<EOF
The <info>%command.name%</info> removes all the expired locks stored in the database

<info>php %command.full_name%</info>
EOF
            );
    }

    /**
     * @see Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        if (!$container->has('integrated_locking.dbal.manager')) {
            return;
        }

        /** @var Manager $service */
        $service = $container->get('integrated_locking.dbal.manager');
        $service->clean();
    }
}
