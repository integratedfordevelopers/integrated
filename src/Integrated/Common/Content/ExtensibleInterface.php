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
interface ExtensibleInterface
{
	/**
	 * Get list of all the extensions names
	 *
	 * @return object[]
	 */
	public function getExtensions();

	/**
	 * Get the extension or null if the extension does not exist
	 *
	 * @param string $name
	 * @return object|null
	 */
	public function getExtension($name);

	/**
	 * Check if the extension exists
	 *
	 * @param string $name
	 * @return bool
	 */
	public function hasExtension($name);

	/**
	 * Set the extension by name.
	 *
	 * Setting the extension to null will removed it
	 *
	 * @param string $name
	 * @param object|null $value
	 */
	public function setExtension($name, $value);
}