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
interface ConverterSpecificationInterface
{
	public function hasClass($class);

	public function getClasses();

	public function hasField($field);

	public function getField($field);

	public function getFields();

	public function getId();
} 