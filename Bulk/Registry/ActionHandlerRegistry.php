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

use Integrated\Bundle\ContentBundle\Bulk\ActionHandlerInterface;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ActionHandlerRegistry
{
    /**
     * @var ActionHandlerInterface[]
     */
    protected $handlers = [];

    /**
     * @return ActionHandlerInterface[]
     */
    public function getHandlers()
    {
        return $this->handlers;
    }

    /**
     * @param ActionHandlerInterface[] $handlers
     * @return $this
     */
    public function setHandlers($handlers)
    {
        $this->handlers = [];
        foreach ($handlers as $handler) {
            $this->addHandler($handler);
        }

        return $this;
    }

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
     * @param string $name
     * @return bool
     */
    public function hasHandler($name)
    {
        return array_key_exists($name, $this->handlers);
    }

    /**
     * @param string $name
     * @return ActionHandlerInterface|null
     */
    public function getHandler($name)
    {
        if ($this->hasHandler($name)) {
            return $this->handlers[$name];
        }

        return null;
    }
}
