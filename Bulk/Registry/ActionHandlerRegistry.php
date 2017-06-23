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

use Integrated\Bundle\ContentBundle\Bulk\Action\ActionHandlerInterface;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ActionHandlerRegistry
{
    /**
     * @var ActionHandlerInterface[]
     */
    private $handlers = [];

    /**
     * @param ActionHandlerInterface[] $handlers
     */
    public function __construct($handlers)
    {
        $this->handlers = $handlers;
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
     * @return ActionHandlerInterface
     */
    public function getHandler($name)
    {
        if ($this->hasHandler($name)) {
            return $this->handlers[$name];
        }

        throw new \InvalidArgumentException(sprintf('%s is not a registered handler', $name));
    }
}
