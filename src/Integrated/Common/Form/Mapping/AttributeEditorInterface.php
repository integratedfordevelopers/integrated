<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Form\Mapping;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface AttributeEditorInterface extends AttributeInterface
{
	/**
	 * @param string $type
	 * @return self
	 */
	public function setType($type);

	/**
	 * @param array $options
	 * @return self
	 */
	public function setOptions(array $options);

	/**
	 * @param string $name
	 * @param mixed $value
	 * @return self
	 */
	public function setOption($name, $value);
}