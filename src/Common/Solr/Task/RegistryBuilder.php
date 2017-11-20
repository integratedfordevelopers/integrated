<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr\Task;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class RegistryBuilder
{
    /**
     * @var callable[]
     */
    private $handlers = [];

    /**
     * @param string   $class
     * @param callable $handler
     */
    public function addHandler($class, callable $handler)
    {
        $this->handlers[strtolower($class)] = $handler;
    }

    /**
     * @param array $handlers
     */
    public function addHandlers(array $handlers)
    {
        foreach ($handlers as $class => $handler) {
            $this->addHandler($class, $handler);
        }
    }

    /**
     * @return Registry
     */
    public function getRegistry()
    {
        return new Registry($this->handlers);
    }
}
