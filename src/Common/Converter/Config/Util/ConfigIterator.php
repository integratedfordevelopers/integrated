<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Converter\Config\Util;

use Integrated\Common\Converter\Config\ConfigInterface;
use Integrated\Common\Converter\Config\TypeConfigInterface;

use Iterator;

/**
 * This iterator will iterate over the types in the given config.
 *
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ConfigIterator implements Iterator
{
    /**
     * @var TypeConfigInterface[]
     */
    private $types = [];

    /**
     * Constructor.
     *
     * @param ConfigInterface $config
     */
    public function __construct(ConfigInterface $config)
    {
        $this->types = $config->getTypes();
    }

    /**
     * {@inheritdoc}
     *
     * @return TypeConfigInterface
     */
    public function current()
    {
        return current($this->types);
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function next()
    {
        next($this->types);
    }

    /**
     * {@inheritdoc}
     *
     * @return int
     */
    public function key()
    {
        return key($this->types);
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function valid()
    {
        return key($this->types) !== null;
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function rewind()
    {
        reset($this->types);
    }
}
