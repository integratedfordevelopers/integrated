<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\ContentType\Mapping;

use ReflectionClass;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface MetadataInterface
{
	const CONTENT = 'Integrated\\Common\\Content\\ContentInterface';

	/**
	 * @return bool
	 */
	public function isContent();

	/**
	 * @return ReflectionClass
	 */
	public function getReflection();

	/**
	 * @return string
	 */
	public function getClass();

	/**
	 * @return string
	 */
	public function getType();

	/**
	 * @return MetadataFieldInterface[]
	 */
	public function getFields();

	/**
	 * @param string $name
	 * @return MetadataFieldInterface
	 */
	public function getField($name);

	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasField($name);
}