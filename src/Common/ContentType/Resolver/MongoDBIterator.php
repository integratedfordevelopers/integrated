<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\ContentType\Resolver;

use Integrated\Common\ContentType\IteratorInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class MongoDBIterator implements IteratorInterface
{
    private $types;

    /**
     * @param array $types
     */
    public function __construct(array $types)
    {
        $this->types = $types;
    }

    /**
     * {@inheritdoc}
     */
    public function current(): mixed
    {
        return current($this->types) ?: null;
    }

    /**
     * {@inheritdoc}
     */
    public function next(): void
    {
        next($this->types);
    }

    /**
     * {@inheritdoc}
     */
    public function key(): mixed
    {
        return $this->valid() ? $this->current()->getId() : null;
    }

    /**
     * {@inheritdoc}
     */
    public function valid(): bool
    {
        return key($this->types) !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind(): void
    {
        reset($this->types);
    }
}
