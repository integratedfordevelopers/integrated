<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Doctrine\ODM\MongoDB\Mapping;

use Integrated\Doctrine\ODM\MongoDB\Mapping\Locator\ClassLocatorInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ClassTreeMapResolverBuilder
{
    /**
     * @var ClassLocatorInterface
     */
    private $locator;

    /**
     * @var string[]
     */
    private $classes = [];

    /**
     * Constructor.
     *
     * @param ClassLocatorInterface $locator
     */
    public function __construct(ClassLocatorInterface $locator)
    {
        $this->locator = $locator;
    }

    /**
     * Add a the class as a root class for the resolver.
     *
     * @param string $class
     */
    public function addClass($class)
    {
        $this->classes[$class] = $class;
    }

    /**
     * @return ClassTreeMapResolver
     */
    public function getResolver()
    {
        return new ClassTreeMapResolver($this->locator, array_values($this->classes));
    }
}
