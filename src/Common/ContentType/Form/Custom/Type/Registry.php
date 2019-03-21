<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\ContentType\Form\Custom\Type;

use Integrated\Common\ContentType\Form\Custom\TypeInterface;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class Registry implements RegistryInterface
{
    /**
     * @var TypeInterface[]
     */
    protected $types = [];

    /**
     * {@inheritdoc}
     */
    public function add(TypeInterface $type)
    {
        $this->types[spl_object_hash($type)] = $type;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function has(TypeInterface $type)
    {
        return \array_key_exists(spl_object_hash($type), $this->types);
    }

    /**
     * @return TypeInterface[]
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->types);
    }
}
