<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SolrBundle\Iterator;

use Doctrine\Persistence\ObjectManager;
use Iterator;

/**
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
class DetachIterator implements Iterator
{
    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @var Iterator
     */
    private $iterator;

    /**
     * @param Iterator      $iterator
     * @param ObjectManager $manager
     */
    public function __construct(Iterator $iterator, ObjectManager $manager)
    {
        $this->iterator = $iterator;
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        $this->manager->detach($current = $this->iterator->current());

        return $current;
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->iterator->next();
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->iterator->key();
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
        $this->iterator->rewind();
    }
}
