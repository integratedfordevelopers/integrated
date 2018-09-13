<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Normalizer\Processor;

use Exception;
use Integrated\Common\Normalizer\Exception\UnexpectedTypeException;
use ReflectionClass;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ProcessorResolver implements ResolverInterface
{
    /**
     * @var RegistryInterface
     */
    private $registry;

    /**
     * @var ResolvedProcessorFactoryInterface
     */
    private $factory;

    /**
     * resolved processor cache.
     *
     * @var ResolvedProcessorInterface[]
     */
    private $resolved = [];

    /**
     * @param RegistryInterface                 $registry
     * @param ResolvedProcessorFactoryInterface $factory
     */
    public function __construct(RegistryInterface $registry, ResolvedProcessorFactoryInterface $factory)
    {
        $this->registry = $registry;
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function getProcessor($object)
    {
        if (\is_object($object)) {
            $object = \get_class($object);
        }

        if (!\is_string($object)) {
            throw new UnexpectedTypeException($object, 'string or object');
        }

        // Check if we already resolved the processor so that we do not need to create unneeded
        // reflection objects for the given class.

        if ($this->hasInstance($object)) {
            return $this->getInstance($object);
        }

        try {
            $processors = $this->resolve(new ReflectionClass($object));
        } catch (Exception $e) {
            $processors = [];
        }

        return $this->setInstance($object, $this->factory->createProcessor($processors));
    }

    /**
     * @param ReflectionClass $reflection
     *
     * @return array
     */
    protected function resolve(ReflectionClass $reflection)
    {
        $processors = [];

        // resolve the parent first

        if ($parent = $reflection->getParentClass()) {
            $processors = $this->resolve($parent);
        }

        if ($this->registry->hasProcessors($reflection->name)) {
            $processors = array_merge($processors, $this->registry->getProcessors($reflection->name));
        }

        return $processors;
    }

    /**
     * Add a new resolved processor to the to the cache.
     *
     * @param string                     $class
     * @param ResolvedProcessorInterface $processor
     *
     * @return ResolvedProcessorInterface
     */
    protected function setInstance($class, ResolvedProcessorInterface $processor)
    {
        return $this->resolved[$class] = $processor;
    }

    /**
     * Check if the class is already resolved.
     *
     * @param string $class
     *
     * @return bool
     */
    protected function hasInstance($class)
    {
        return isset($this->resolved[$class]);
    }

    /**
     * Get the resolved processor form the cache.
     *
     * @param string $class
     */
    protected function getInstance($class)
    {
        return isset($this->resolved[$class]) ? $this->resolved[$class] : null;
    }
}
