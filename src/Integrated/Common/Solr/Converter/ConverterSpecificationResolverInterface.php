<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr\Converter;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface ConverterSpecificationResolverInterface
{
	/**
	 * @param $class
	 * @return bool
	 */
	public function hasSpecification($class);

	/**
	 * @param $class
	 * @return ConverterSpecificationInterface
	 */
	public function getSpecification($class);
} 