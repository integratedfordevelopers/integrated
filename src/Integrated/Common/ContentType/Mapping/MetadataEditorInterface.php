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
interface MetadataEditorInterface extends MetadataInterface
{
	/**
	 * @param string $type
	 * @return self
	 */
	public function setType($type);

	/**
	 * @param string $name
	 * @return MetadataFieldEditorInterface
	 */
	public function newField($name);

	/**
	 * @param MetadataFieldInterface $field
	 * @return self
	 */
	public function addField(MetadataFieldInterface $field);
}