<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Storage\Command;

use Integrated\Common\Storage\ManagerInterface;

/**
 * A simple implementation for for basic support for the message bus (queue) strategy.
 *
 * @author Johnny Borg <johnny@e-active.nl>
 */
interface CommandInterface
{
    /**
     * @param ManagerInterface $manager
     */
    public function execute(ManagerInterface $manager);
}
