<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Storage\Handler;

use Integrated\Bundle\StorageBundle\Storage\Command\CommandInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class QueuedCommandBus implements QueuedCommandBusInterface
{
    /**
     * @var CommandInterface[]
     */
    protected $queue = [];

    /**
     * @param CommandInterface $commandInterface
     */
    public function add(CommandInterface $commandInterface)
    {
        $this->queue[] = $commandInterface;
    }

    /**
     *
     */
    public function execute()
    {

    }

}
