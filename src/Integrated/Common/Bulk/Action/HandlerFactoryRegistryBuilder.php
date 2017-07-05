<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Bulk\Action;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class HandlerFactoryRegistryBuilder
{
    /**
     * @var HandlerFactoryInterface[]
     */
    private $factories = [];

    /**
     * @param string                  $class
     * @param HandlerFactoryInterface $factory
     */
    public function addFactory($class, HandlerFactoryInterface $factory)
    {
        $this->factories[$class] = $factory;
    }

    /**
     * @return HandlerFactoryRegistry
     */
    public function getRegistry()
    {
        return new HandlerFactoryRegistry($this->factories);
    }
}
