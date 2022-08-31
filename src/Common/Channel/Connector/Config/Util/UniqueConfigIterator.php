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

use Integrated\Common\Channel\Connector\Config\ConfigInterface;
use IteratorIterator;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class UniqueConfigIterator extends IteratorIterator
{
    /**
     * @var array
     */
    private $accepted = [];

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

        $this->accepted = [];

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
        $config = $this->current();

        if ($config instanceof ConfigInterface && !\array_key_exists($config->getName(), $this->accepted)) {
            return $this->accepted[$config->getName()] = true;
        }

        return false;
    }
}
