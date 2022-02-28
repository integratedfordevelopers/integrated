<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Converter\Config\Util;

use Integrated\Common\Converter\Config\ConfigInterface;
use Integrated\Common\Converter\Config\TypeConfigInterface;
use Iterator;

/**
 * This iterator will iterate over the types in the config starting at the root parent
 * and then moving up.
 *
 * @see ConfigIterator
 *
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ParentAwareConfigIterator implements Iterator
{
    /**
     * @var ConfigIterator[]
     */
    private $iterators = [];

    /**
     * null means the iterator reached its end.
     *
     * @var ConfigIterator
     */
    private $current = null;

    /**
     * @var int
     */
    private $counter = 0;

    /**
     * Constructor.
     *
     * @param ConfigInterface $config
     */
    public function __construct(ConfigInterface $config)
    {
        $this->iterators[] = new ConfigIterator($config);

        while ($config = $config->getParent()) {
            array_unshift($this->iterators, new ConfigIterator($config));
        }

        $this->rewind(); // prepare for first run
    }

    /**
     * {@inheritdoc}
     *
     * @return TypeConfigInterface
     */
    public function current()
    {
        return $this->current ? $this->current->current() : false;
    }

    /**
     * {@inheritdoc}
     */
    public function next(): void
    {
        if (!$this->current) {
            return;
        }

        ++$this->counter;
        $this->current->next();

        $this->validate();
    }

    /**
     * {@inheritdoc}
     *
     * @return null|int
     */
    public function key(): ?int
    {
        return $this->current ? $this->counter : null;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function valid(): bool
    {
        return $this->current ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind(): void
    {
        reset($this->iterators);

        $this->counter = 0;
        $this->current = current($this->iterators);
        $this->current->rewind(); // there is always one iterator so no need to check if current is empty

        $this->validate();
    }

    /**
     * check if the current iterator is still valid or if not move to the next one
     * until we find one that is.
     */
    protected function validate()
    {
        if ($this->current->valid()) {
            return;
        }

        do {
            if ($this->current = next($this->iterators)) {
                $this->current->rewind();
            }
        } while ($this->current && !$this->current->valid());
    }
}
