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
interface DriverInterface
{
	/**
	 * Get all the classnames of possible content classes
	 *
	 * @return string[]
	 */
	public function getAllClassNames();

	/**
	 * Load the metadata for the given class and store them in the metadata class
	 *
	 * @param $class
	 * @param MetadataEditorInterface $metadata
	 */
	public function loadMetadataForClass($class, MetadataEditorInterface $metadata);
}