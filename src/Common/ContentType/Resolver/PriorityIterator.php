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

use AppendIterator;
use Integrated\Common\ContentType\IteratorInterface;
use Integrated\Common\ContentType\ResolverInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class PriorityIterator implements IteratorInterface
{
    /**
     * @var AppendIterator
     */
    private $iterator;

    /**
     * @var array
     */
    private $accepted = [];

    /**
     * Constructor.
     *
     * @param ResolverInterface[] $resolvers
     */
    public function __construct(array $resolvers)
    {
        $this->iterator = new AppendIterator();

        foreach ($resolvers as $resolver) {
            $this->iterator->append($resolver->getTypes());
        }

        $this->rewind();
    }

    /**
     * {@inheritdoc}
     */
    public function current(): mixed
    {
        return $this->iterator->current();
    }

    /**
     * {@inheritdoc}
     */
    public function next(): void
    {
        if (!$this->iterator->valid()) {
            return;
        }

        $this->iterator->next();

        $this->validate();
    }

    /**
     * {@inheritdoc}
     */
    public function key(): mixed
    {
        return $this->iterator->valid() ? $this->iterator->key() : null;
    }

    /**
     * {@inheritdoc}
     */
    public function valid(): bool
    {
        return $this->iterator->valid();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind(): void
    {
        $this->accepted = [];
        $this->iterator->rewind();

        $this->validate();
    }

    /**
     * Check if the current content type is already accepted from an other resolver iterator. When
     * a type is found that is already accepted move to the next content type in the iterator until
     * a not yet accepted content type is found.
     */
    private function validate()
    {
        while ($this->iterator->valid()) {
            $type = $this->current()->getId();

            if (!\array_key_exists($type, $this->accepted)) {
                $this->accepted[$type] = true;

                return;
            }

            $this->iterator->next();
        }
    }
}
