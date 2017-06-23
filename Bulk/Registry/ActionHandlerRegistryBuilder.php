<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Bulk\Registry;

use Integrated\Bundle\ContentBundle\Bulk\ActionHandler\ActionHandlerInterface;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ActionHandlerRegistryBuilder
{
    /**
     * @var ActionHandlerInterface[]
     */
    protected $handlers = [];

    /**
     * @param ActionHandlerInterface $handler
     * @return $this
     */
    public function addHandler(ActionHandlerInterface $handler)
    {
        $this->handlers[get_class($handler)] = $handler;
        return $this;
    }

    /**
     * @return ActionHandlerRegistry
     */
    public function getRegistry()
    {
        return new ActionHandlerRegistry($this->handlers);
    }
}
