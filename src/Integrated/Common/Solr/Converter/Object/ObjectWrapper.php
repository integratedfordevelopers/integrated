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
use ReflectionClass;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ObjectWrapper implements WrapperInterface
{
	/**
	 * @var mixed
	 */
	protected $value;

	public function __construct($value)
	{
		$this->value = $value;
	}

	public function value()
	{
		if (is_scalar($this->value)) {
			return $this->value;
		}

		return null;
	}

	public function raw()
	{
		return $this->value;
	}

    public function int()
    {
        return (int) $this->value;
    }

    public function multi()
	{
		// check if its a array else nothing changes..

		if ($this->isArray()) {
			$values = array();

			foreach ($this->value as $value) {
				$values[] = new self($value);
			}

			return new MultiWrapper($values);
		}

		return $this;
	}

	public function concat($glue, $pieces = null, $keepempty = false)
	{
		// variable argument order stuff
		// so check and reorder variables if required.

		switch (func_num_args()) {
			case 1:
				$pieces = $glue;
				$glue = '';

				break;

			case 2:

				if (!is_array($pieces)) {
					$keepempty = $pieces;
					$pieces = $glue;
					$glue = '';
				}

				break;
		}

		$glue = (string) $glue;
		$pieces = (array) $pieces;
		$keepempty = (bool) $keepempty;

		// start collection all the values
		// $pieces that start with a @ are considered variable names.

		$values = array();

		foreach ($pieces as $value) {
			if ($value[0] == '@') {
				$value = $this->__get(substr($value, 1))->value();
			}

			$values[] = $value;
		}

		if (!$keepempty) {
			$values = array_filter($values);
		}

		return new self(implode($glue, $values));
	}

	public function isEmpty()
	{
		return empty($this->value);
	}

	public function isValue()
	{
		return is_scalar($this->value);
	}

	public function isArray()
	{
		return is_array($this->value) || $this->value instanceof ArrayAccess;
	}

	public function isScalar()
	{
		return is_scalar($this->value);
	}

	public function isObject()
	{
		return is_object($this->value);
	}

	public function offsetExists($offset)
	{
		if ($this->isArray()) {
			return isset($this->value[$offset]);
		}

		return false;
	}

	public function offsetGet($offset)
	{
		if ($this->offsetExists($offset)) {
			return new self(null);
		}

		return new self($this->value[$offset]);
	}

	public function offsetSet($offset, $value) { /* not supported. */ }

	public function offsetUnset($offset) { /* not supported. */ }

	public function __isset($name)
	{
		if ($this->isScalar()) {
			return false;
		}

		$value = $this->value;

		// if the $value is a array get the first entry in that array this
		// will be done only ones as if its a array in a array then the config
		// should reflect that.

		if ($this->isArray()) {
			$value = isset($value[0]) ? $value[0] : null;
		}

		// So it has to be a object or else its not possible to call a property
		// in the first place

		if (is_object($value)) {
			$reflection = new ReflectionClass($value);

			if ($reflection->hasProperty($name) && ($prop = $reflection->getProperty($name)) && $prop->isPublic()) {
				return $prop->getValue($value) !== null;
			}

			$method = 'get' . ucfirst($name);

			if ($reflection->hasMethod($method) && ($method = $reflection->getMethod($method)) && $method->isPublic() && $method->getNumberOfRequiredParameters() == 0) {
				return $method->invoke($value) !== null;
			}

			// @todo check for __get, __isset or __call
		}

		return false;
	}

	public function __get($name)
	{
		if ($this->isScalar()) {
			return new self(null);
		}

		$value = $this->value;

		// if the $value is a array get the first entry in that array this
		// will be done only ones as if its a array in a array then the config
		// should reflect that.

		if ($this->isArray()) {
			$value = isset($value[0]) ? $value[0] : null;
		}

		// So it has to be a object or else its not possible to call a property
		// in the first place

		if (is_object($value)) {
			$reflection = new ReflectionClass($value);

			if ($reflection->hasProperty($name) && ($prop = $reflection->getProperty($name)) && $prop->isPublic()) {
				return new self($prop->getValue($value));
			}

			$method = 'get' . ucfirst($name);

			if ($reflection->hasMethod($method) && ($method = $reflection->getMethod($method)) && $method->isPublic() && $method->getNumberOfRequiredParameters() == 0) {
				return new self($method->invoke($value));
			}

			// @todo check for __get, __isset or __call
		}

		return new self(null);
	}

	public function __set($name, $value) { /* not supported. */ }

	public function __unset($name) { /* not supported. */ }

	public function __call($name, array $arguments)
	{
		if ($this->isScalar()) {
			return new self(null);
		}

		$value = $this->value;

		// if the $value is a array get the first entry in that array this
		// will be done only ones as if its a array in a array then the config
		// should reflect that.

		if ($this->isArray()) {
			$value = isset($value[0]) ? $value[0] : null;
		}

		if (is_object($value)) {
			$reflection = new ReflectionClass($value);

			if ($reflection->hasMethod($name) && ($method = $reflection->getMethod($name)) && $method->isPublic() && $method->getNumberOfRequiredParameters() <= count($arguments)) {
				return new self($method->invokeArgs($value, $arguments));
			}
		}

		return new self(null);
	}
}