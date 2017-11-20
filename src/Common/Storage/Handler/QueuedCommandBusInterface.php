<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Storage\Handler;

use Integrated\Common\Storage\Command\CommandInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
interface QueuedCommandBusInterface
{
    /**
     * @param CommandInterface $commandInterface
     */
    public function add(CommandInterface $commandInterface);
}
