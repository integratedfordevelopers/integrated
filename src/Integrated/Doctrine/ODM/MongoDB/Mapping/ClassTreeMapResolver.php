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
class ClassTreeMapResolver implements DiscriminatorMapResolverInterface
{
    /**
     * @var ClassLocatorInterface
     */
    private $locator;

    /**
     * @var string[]
     */
    private $map_roots = [];

    /**
     * @var string[][]
     */
    private $map = null;

    /**
     * @param ClassLocatorInterface $locator
     */
    public function __construct(ClassLocatorInterface $locator, array $roots)
    {
        $this->locator = $locator;
        $this->map_roots = $roots;
    }

    /**
     * lazy load the class maps.
     */
    protected function load()
    {
        $this->map = array_fill_keys($this->map_roots, []);

        foreach ($this->locator->getClassNames() as $class) {
            foreach ($this->map_roots as $root) {
                if (is_a($class, $root, true)) {
                    $this->map[$root][$class] = $class;
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($class)
    {
        $root = $this->resolveRoot($class);

        if (null === $this->map && $root) {
            $this->load();
        }

        return $root && isset($this->map[$root]) ? $this->map[$root] : null;
    }

    /**
     * @param string $class
     *
     * @return null | string
     */
    protected function resolveRoot($class)
    {
        foreach ($this->map_roots as $root) {
            if (is_a($class, $root, true)) {
                return $root;
            }
        }

        return null;
    }
}
