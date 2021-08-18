<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Normalizer;

use Countable;
use IteratorAggregate;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface ContainerInterface extends Countable, IteratorAggregate
{
    /**
     * Add the value to the given container key.
     *
     * @param string                           $key
     * @param string|float|int|bool|array|null $value
     *
     * @return ContainerInterface
     */
    public function add($key, $value);

    /**
     * Set the value for the given container key.
     *
     * @param string                           $key
     * @param string|float|int|bool|array|null $value
     *
     * @return ContainerInterface
     */
    public function set($key, $value);

    /**
     * Remove the value for the given container key.
     *
     * @param $key
     *
     * @return ContainerInterface
     */
    public function remove($key);

    /**
     * Check if a container key exists.
     *
     * @param $key
     *
     * @return bool
     */
    public function has($key);

    /**
     * Get the value from the given container key.
     *
     * @param $key
     *
     * @return mixed[]
     */
    public function get($key);

    /**
     * Clear all the container data.
     *
     * @return ContainerInterface
     */
    public function clear();

    /**
     * Return the container data as a associative array.
     *
     * @return array
     */
    public function toArray();
}
