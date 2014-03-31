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

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface MetadataFieldInterface
{
	/**
	 * @return string
	 */
	public function getName();

	/**
	 * @return string
	 */
	public function getType();

	/**
	 * @return array
	 */
	public function getOptions();

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function getOption($name);

	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasOption($name);
} 