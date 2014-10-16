<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Converter;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface ContainerInterface extends \Countable, \IteratorAggregate
{
    /**
   	 * Add the value to the container
   	 *
   	 * @param null | string | float | int | bool $value
   	 * @return self
   	 */
    public function add($key, $value);

    /**
   	 * Set the value for the given key
   	 *
   	 * @param string $key
   	 * @param null | string | float | int | bool $value
     *
   	 * @return self
   	 */
    public function set($key, $value);

    /**
   	 * Remove the value for the given key
   	 *
   	 * @param $key
   	 * @return self
   	 */
    public function remove($key);

    /**
   	 * Check if a container key exists.
   	 *
   	 * @param $key
   	 * @return bool
   	 */
    public function has($key);

    /**
   	 * Get the value from the given key
   	 *
   	 * @param $key
   	 * @return mixed[]
   	 */
    public function get($key);

    /**
   	 * Clear all the container data
   	 *
   	 * @return self
   	 */
    public function clear();

    /**
   	 * @return array
   	 */
    public function toArray();
}
