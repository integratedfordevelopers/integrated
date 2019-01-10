<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Normalizer;

use ArrayIterator;
use Integrated\Common\Normalizer\Exception\UnexpectedTypeException;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Container implements ContainerInterface
{
    const EMPTY_TYPE = 0;
    const VALUE_TYPE = 1;
    const ARRAY_TYPE = 2;

    /**
     * @var array
     */
    private $data = [];

    /**
     * @var array
     */
    private $type = [];

    /**
     * {@inheritdoc}
     */
    public function add($key, $value)
    {
        if (!isset($this->type[$key]) || $this->type[$key] !== self::ARRAY_TYPE) {
            $this->data[$key] = empty($this->type[$key]) ? [] : [$this->data[$key]];
            $this->type[$key] = self::ARRAY_TYPE;
        }

        $this->data[$key][] = self::validateAndReturn($value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        $this->data[$key] = self::validateAndReturn($value);
        $this->type[$key] = self::VALUE_TYPE;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key)
    {
        unset($this->data[$key]);
        unset($this->type[$key]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        return array_key_exists($key, $this->data) ? $this->data[$key] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->data = [];
        $this->type = [];

        return $this;
    }

    /**
     * Check if the value is one of the allowed types or throw a exception.
     *
     * @param mixed $value
     *
     * @trows UnexpectedTypeException if $value is not a scalar or array type
     */
    protected static function validateAndReturn($value)
    {
        if ($value === null || is_scalar($value) || \is_array($value)) {
            return $value;
        }

        throw new UnexpectedTypeException($value, 'null, string, float, int, bool or array');
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return \count($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new ArrayIterator($this->data);
    }
}
