<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr\Indexer;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Batch implements Countable, IteratorAggregate
{
    /**
     * @var BatchOperation[]
     */
    private $batch = [];

    /**
     * Add the given batch operation to the batch.
     *
     * @param BatchOperation $operation
     */
    public function add(BatchOperation $operation)
    {
        $this->batch[] = $operation;
    }

    /**
     * Remove the given batch operation from the batch.
     *
     * @param BatchOperation $operation
     */
    public function remove(BatchOperation $operation)
    {
        foreach ($this->batch as $key => $value) {
            if ($operation === $value) {
                unset($this->batch[$key]);
            }
        }

        $this->batch = array_values($this->batch); // reorder keys
    }

    /**
     * Clear all the batch operation from the batch.
     */
    public function clear()
    {
        $this->batch = [];
    }

    /**
     * Return the number of batch operations.
     *
     * @return int
     */
    public function count(): int
    {
        return \count($this->batch);
    }

    /**
     * Get a iterator to walk of the batch operations.
     *
     * @return BatchOperation[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->batch);
    }
}
