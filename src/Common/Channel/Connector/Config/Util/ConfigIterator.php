<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Channel\Connector\Config\Util;

use ArrayIterator;
use Integrated\Common\Channel\Connector\Config\ConfigInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ConfigIterator extends ArrayIterator
{
    /**
     * {@inheritdoc}
     */
    public function key(): ?string
    {
        if ($this->valid()) {
            return $this->current()->getName();
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function next(): void
    {
        do {
            parent::next();
        } while ($this->valid() && !$this->accept());
    }

    /**
     * {@inheritdoc}
     */
    public function rewind(): void
    {
        parent::rewind();

        while ($this->valid() && !$this->accept()) {
            parent::next();
        }
    }

    /**
     * Check whether the current element of the iterator is acceptable.
     *
     * @return bool
     */
    protected function accept()
    {
        if ($this->current() instanceof ConfigInterface) {
            return true;
        }

        return false;
    }
}
