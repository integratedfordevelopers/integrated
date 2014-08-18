<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface RegistryInterface extends \ArrayAccess, \Countable, \IteratorAggregate
{
	/**
	 * @return array
	 */
	public function toArray();

	/**
	 * Add the value to the registry
	 *
	 * @param mixed $value
	 * @return self
	 */
	public function add($value);

	/**
	 * Set the value for the given key
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return self
	 */
	public function set($key, $value);

	/**
	 * Get the value from the given key
	 *
	 * @param $key
	 * @return mixed
	 */
	public function get($key);

	/**
	 * Remove the value for the given key
	 *
	 * @param $key
	 * @return self
	 */
	public function remove($key);

	/**
	 * Check if a registry key exists.
	 *
	 * @param $key
	 * @return bool
	 */
	public function has($key);

	/**
	 * Clear all the registry data
	 *
	 * @return self
	 */
	public function clear();
}