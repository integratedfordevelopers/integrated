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

use Integrated\Common\ContentType\ContentTypeIteratorInterface;
use Integrated\Common\ContentType\ContentTypeResolverInterface;

use AppendIterator;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class PriorityIterator implements ContentTypeIteratorInterface
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
     * @var int
     */
    private $counter = 0;

    /**
     * Constructor.
     *
     * @param ContentTypeResolverInterface[] $resolvers
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
    public function current()
    {
        return $this->iterator->current();
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        if (!$this->iterator->valid()) {
            return;
        }

        $this->counter++;
        $this->iterator->next();

        $this->validate();
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->iterator->valid() ? $this->counter : null;
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return $this->iterator->valid();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->accepted = [];
        $this->counter = 0;

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
            $cur = $this->current();
            $cur = json_encode(['class' => $cur->getClass(), 'type' => $cur->getType()]);

            if (!array_key_exists($cur, $this->accepted)) {
                $this->accepted[$cur] = $this->counter;

                return;
            }

            $this->iterator->next();
        }
    }
}
