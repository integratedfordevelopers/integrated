<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\FormConfig\Manager;

use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\FormConfig\FormConfigFactoryInterface;
use Integrated\Common\FormConfig\FormConfigInterface;
use Iterator;

class DefaultAwareIterator implements Iterator
{
    /**
     * @var array
     */
    private $configs = [];

    /**
     * @param ContentTypeInterface       $type
     * @param Iterator                   $iterator
     * @param FormConfigFactoryInterface $factory
     */
    public function __construct(ContentTypeInterface $type, Iterator $iterator, FormConfigFactoryInterface $factory)
    {
        $default = null;

        foreach ($iterator as $config) {
            if (!$config instanceof FormConfigInterface) {
                continue;
            }

            if ($default === null && $config->getId()->getKey() === 'default') {
                $default = $config;
            } else {
                $this->configs[] = $config;
            }
        }

        if (!$default) {
            $default = $factory->create($type, 'default');
        }

        array_unshift($this->configs, $default);
    }

    /**
     * {@inheritDoc}
     */
    public function current()
    {
        $config = current($this->configs);

        return $config === false ? null : $config;
    }

    /**
     * {@inheritDoc}
     */
    public function next()
    {
        next($this->configs);
    }

    /**
     * {@inheritDoc}
     */
    public function key()
    {
        return key($this->configs);
    }

    /**
     * {@inheritDoc}
     */
    public function valid()
    {
        return key($this->configs) !== null;
    }

    /**
     * {@inheritDoc}
     */
    public function rewind()
    {
        reset($this->configs);
    }
}
