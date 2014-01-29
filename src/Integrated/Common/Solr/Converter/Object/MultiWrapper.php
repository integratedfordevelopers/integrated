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

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class MultiWrapper implements WrapperInterface
{
	/**
	 * @var WrapperInterface[]
	 */
	protected $values;

	public function __construct(array $values)
	{
		$this->values = $values;
	}

	public function value()
	{
		$values = array();

		foreach ($this->values as $value) {
			$values[] = $value->value();
		}

		return array_filter($values);
	}

	public function raw()
	{
		$values = array();

		foreach ($this->values as $value) {
			$values[] = $value->raw();
		}

		return $values;
	}

	public function multi()
	{
		$changed = false;
		$values = array();

		foreach ($this->values as $key => $value) {
			$values[$key] = $value->multi();

			// if nothing changed then $this can be returned.

			if ($values[$key] !== $value) {
				$changed = true;
			}
		}

		if ($changed){
			return new MultiWrapper($values);
		}

		return $this;
	}

	public function isEmpty()
	{
		return empty($this->values);
	}

	public function isValue()
	{
		return false;
	}

	public function isArray()
	{
		return false;
	}

	public function isScalar()
	{
		return false;
	}

	public function isObject()
	{
		return false;
	}

	public function offsetExists($offset)
	{
		foreach ($this->values as $value) {
			if (!$value->offsetExists($offset)) {
				return false;
			}
		}

		return !$this->isEmpty();
	}

	public function offsetGet($offset)
	{
		$values = array();

		foreach ($this->values as $value) {
			$values[] = $value->offsetGet($offset);
		}

		return new MultiWrapper($values);
	}

	public function offsetSet($offset, $value) { /* not supported. */ }

	public function offsetUnset($offset) { /* not supported. */ }

	public function __isset($name)
	{
		foreach ($this->values as $value) {
			if (!$value->__isset($name)) {
				return false;
			}
		}

		return !$this->isEmpty();
	}

	public function __get($name)
	{
		$values = array();

		foreach ($this->values as $value) {
			$values[] = $value->__get($name);
		}

		return new MultiWrapper($values);
	}

	function __set($name, $value) { /* not supported. */ }

	function __unset($name) { /* not supported. */ }

	public function __call($name, array $arguments)
	{
		$values = array();

		foreach ($this->values as $value) {
			$values[] = $value->__call($name, $arguments);
		}

		return new MultiWrapper($values);
	}
} 