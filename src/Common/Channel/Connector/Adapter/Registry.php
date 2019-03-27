<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Channel\Connector\Adapter;

use Integrated\Common\Channel\Connector\AdapterInterface;
use Integrated\Common\Channel\Exception\InvalidArgumentException;
use Integrated\Common\Channel\Exception\UnexpectedTypeException;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Registry implements RegistryInterface
{
    /**
     * @var AdapterInterface[]
     */
    private $adapters;

    /**
     * Constructor.
     *
     * @param AdapterInterface[] $adapters
     */
    public function __construct(array $adapters)
    {
        $this->adapters = $adapters;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdapter($name)
    {
        if (!\is_string($name)) {
            throw new UnexpectedTypeException($name, 'string');
        }

        if ($this->hasAdapter($name)) {
            return $this->adapters[$name];
        }

        throw new InvalidArgumentException(sprintf('Could not load adaptor "%s"', $name));
    }

    /**
     * {@inheritdoc}
     */
    public function hasAdapter($name)
    {
        if (!\is_string($name)) {
            throw new UnexpectedTypeException($name, 'string');
        }

        if (isset($this->adapters[$name])) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdapters()
    {
        return $this->adapters;
    }
}
