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

use Integrated\Common\Bulk\Exception\InvalidArgumentException;
use Integrated\Common\Bulk\Exception\UnexpectedTypeException;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class HandlerFactoryRegistry
{
    /**
     * @var HandlerFactoryInterface[]
     */
    private $factories = [];

    /**
     * Constructor.
     *
     * @param HandlerFactoryInterface[] $factories
     */
    public function __construct(array $factories)
    {
        $this->factories = $factories;
    }

    /**
     * @param string $class
     *
     * @return bool
     */
    public function hasFactory($class)
    {
        if (!\is_string($class)) {
            throw new UnexpectedTypeException($class, 'string');
        }

        if (isset($this->factories[$class])) {
            return true;
        }

        return false;
    }

    /**
     * @param string $class
     *
     * @return HandlerFactoryInterface
     */
    public function getFactory($class)
    {
        if (!\is_string($class)) {
            throw new UnexpectedTypeException($class, 'string');
        }

        if (isset($this->factories[$class])) {
            return $this->factories[$class];
        }

        throw new InvalidArgumentException(sprintf('No handler factory found for class "%s"', $class));
    }
}
