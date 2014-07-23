<?php
/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr\Converter\Object;

use ArrayAccess;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface WrapperInterface extends ArrayAccess
{
//	/**
//	 * @param mixed $value
//	 */
//	public function __construct($value);

	/**
	 * return the scalar value of the object.
	 *
	 * if the value is not a scalar value return null.
	 *
	 * @return mixed
	 */
	public function value();

	/**
	 * return the raw value no mather the type.
	 *
	 * @return mixed
	 */
	public function raw();

	/**
	 * The value is assumed to be array and all the actions
	 * after this will be done on the whole array. The value
	 * method wil return a array with all the none null values
	 *
	 * @return WrapperInterface
	 */
	public function multi();

	/**
	 * @param $glue
	 * @param $pieces
	 * @param bool $keepempty
	 * @return mixed
	 */
	public function concat($glue, $pieces = null, $keepempty = false);

	/**
	 * @param $glue
	 * @param null $pieces
	 * @param bool $keepempty
	 * @return mixed
	 */
	public function combine($glue, $pieces = null, $keepempty = false);

//	public function flatten();

	/**
	 * check if the value is empty
	 *
	 * @return bool
	 */
	public function isEmpty();

	/**
	 * check if the value is a scalar
	 *
	 * @return bool
	 */
	public function isValue();

	/**
	 * check if the value is a array
	 *
	 * @return bool
	 */
	public function isArray();

	/**
	 * check if the value is a scalar
	 *
	 * @return bool
	 */
	public function isScalar();

	/**
	 * check if the value is a object
	 *
	 * @return bool
	 */
	public function isObject();

	public function __isset($name);

	public function __get($name);

	public function __call($name, array $arguments);
}