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
trait ExtensibleTrait
{
	protected $extensions = array();

	/**
	 * Get list of all the extensions names
	 *
	 * @return string[]
	 */
	public function getExtensions()
	{
		return array_keys($this->extensions);
	}

	/**
	 * Get the extension or null if the extension does not exist
	 *
	 * @param string $name
	 * @return object|null
	 */
	public function getExtension($name)
	{
		if ($this->hasExtension($name)) {
			return $this->extensions[$name];
		}

		return null;
	}

	/**
	 * Check if the extension exists
	 *
	 * @param string $name
	 * @return bool
	 */
	public function hasExtension($name)
	{
		return isset($this->extensions[$name]);
	}

	/**
	 * Set the extension by name.
	 *
	 * Setting the extension to null will removed it
	 *
	 * @param string $name
	 * @param object|null $value
	 */
	public function setExtension($name, $value)
	{
		if ($value === null) {
			unset($this->extensions[$name]);
		} else {
			$this->extensions[$name] = $value;
		}
	}
}