<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Converter\Config;

use Exception;
use ReflectionClass;

use Integrated\Common\Converter\Exception\UnexpectedTypeException;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ConfigResolver implements ConfigResolverInterface
{
    /**
     * @var TypeProviderInterface
     */
    private $provider;

    /**
     * @var ConfigInterface[]
     */
    private $resolved = [];

    /**
     * @param TypeProviderInterface $provider
     */
    public function __construct(TypeProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig($class)
    {
        if (!is_string($class)) {
            throw new UnexpectedTypeException($class, 'string');
        }

        // Check if we already resolved the config so that we do not need to create unneeded
        // reflection objects for the given class.

        if ($this->hasInstance($class)) {
            return $this->getInstance($class);
        }

        try {
            $reflection = new ReflectionClass($class);
        } catch (Exception $e) {
            return $this->setInstance($class, null);
        }

        // Always set the instance as reflection does give the correct class name with the
        // correct caps but that does not mean that the $class was written correctly. so also
        // add it with the given class name in case its different

        return $this->setInstance($class, $this->resolve($reflection));
    }

    /**
     * @param ReflectionClass $reflection
     * @return null | ConfigInterface
     */
    protected function resolve(ReflectionClass $reflection)
    {
        if ($this->hasInstance($reflection->name)) {
            return $this->getInstance($reflection->name);
        }

        if ($parent = $reflection->getParentClass()) {
            $parent = $this->resolve($parent);
        }

        if ($types = $this->provider->getTypes($reflection->name)) {
            $config = $this->newInstance($reflection->name, $types, $parent ?: null);
        } else {
            $config = $this->setInstance($reflection->name, $parent ?: null);
        }

        return $config;
    }

    /**
     * Add a new config to the to the resolved instances
     *
     * @param string $class
     * @param ConfigInterface $config
     *
     * @return ConfigInterface
     */
    protected function setInstance($class, ConfigInterface $config = null)
    {
        return $this->resolved[$class] = $config;
    }

    /**
     * This will create a new config instance and added it to the other resolved instances.
     *
     * @param string $class
     * @param TypeConfigInterface[] $types
     * @param ConfigInterface $parent
     *
     * @return ConfigInterface
     */
    protected function newInstance($class, array $types, ConfigInterface $parent = null)
    {
        return $this->setInstance($class, new Config($types, $parent));
    }

    /**
     * Check if the class got a resolved version of the config.
     *
     * @param string $class
     * @return bool
     */
    protected function hasInstance($class)
    {
        // use array_key_exists as a value of null is also a resolved class instance but one
        // the does not have any config.

        return array_key_exists($class, $this->resolved);
    }

    /**
     * Get the config for the given class or null if not found.
     *
     * Note: null does not mean that the class is not resolved yet as it could also mean that
     * there was no config for the class. So always check with hasInstance if the class is
     * already resolved or not.
     *
     * @param string $class
     * @return null | ConfigInterface
     */
    protected function getInstance($class)
    {
        return isset($this->resolved[$class]) ? $this->resolved[$class] : null;
    }
}
