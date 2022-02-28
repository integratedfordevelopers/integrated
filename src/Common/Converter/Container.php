<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Converter;

use ArrayIterator;
use Integrated\Common\Converter\Exception\UnexpectedTypeException;
use Traversable;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Container implements ContainerInterface
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * {@inheritdoc}
     */
    public function add($key, $value)
    {
        if ($value === null) {
            return $this;
        }

        $this->data[$key][] = self::validateAndReturn($value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        $this->remove($key);

        if ($value === null) {
            return $this;
        }

        return $this->add($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key)
    {
        unset($this->data[$key]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->data = [];

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * Check if the value is one of the allowed types or throw a exception.
     *
     * @param mixed $value
     *
     * @trows UnexpectedTypeException if $value is not a scalar type
     */
    protected static function validateAndReturn($value)
    {
        if (is_scalar($value)) {
            return $value;
        }

        throw new UnexpectedTypeException($value, 'null, string, float, int or bool');
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return \count($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->data);
    }
}
