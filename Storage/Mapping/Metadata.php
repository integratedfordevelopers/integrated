<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Storage\Mapping;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Metadata
{
    /**
     * @var string
     */
    protected $class;

    /**
     * @var PropertyInterface[]
     */
    protected $properties = [];

    /**
     * @param string              $class
     * @param PropertyInterface[] $properties
     */
    public function __construct($class, array $properties)
    {
        $this->class = $class;
        $this->properties = $properties;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->class;
    }

    /**
     * @return PropertyInterface[]
     */
    public function getProperties()
    {
        return $this->properties;
    }
}
