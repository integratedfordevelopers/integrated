<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Channel\Connector\Config;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface OptionsInterface extends \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * @return array
     */
    public function toArray();

    /**
     * Set the value for the given key
     *
     * @param string $key
     * @param mixed $value
     *
     * @return self
     */
    public function set($key, $value);

    /**
     * Get the value from the given key
     *
     * @param $key
     *
     * @return mixed
     */
    public function get($key);

    /**
     * Remove the value for the given key
     *
     * @param $key
     *
     * @return self
     */
    public function remove($key);

    /**
     * Check if a option key exists.
     *
     * @param $key
     *
     * @return bool
     */
    public function has($key);

    /**
     * Clear all the options data
     *
     * @return self
     */
    public function clear();
}
