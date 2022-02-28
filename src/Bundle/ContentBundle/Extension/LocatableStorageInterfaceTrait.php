<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Extension;

/**
 * This trait is a implementation of \ArrayAccess in the std library of php.
 * To force the image handling to treat this as a locatable object a path must start with @.
 * The lookup will be done trough the storage bundle. The class that uses this trait must implement \ArrayAccess.
 *
 * @author Johnny Borg <johnny@e-active.nl>
 */
trait LocatableStorageInterfaceTrait
{
    /**
     * @return string
     */
    abstract public function getPathname();

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getPathname();
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset): bool
    {
        return \strlen($this->getPathname()) > ($offset + 1);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        if (0 === $offset) {
            return '@';
        }

        return substr($this->getPathname(), ($offset + 1), 1);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function offsetSet($offset, $value): void
    {
        throw new \Exception('Setting properties is forbidden');
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function offsetUnset($offset): void
    {
        throw new \Exception('Setting properties is forbidden');
    }
}
