<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Channel\Connector\Adaptor;

use Integrated\Common\Channel\Connector\AdaptorInterface;
use Integrated\Common\Channel\Exception\InvalidArgumentException;
use Integrated\Common\Channel\Exception\UnexpectedTypeException;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Registry implements RegistryInterface
{
    /**
     * @var AdaptorInterface[]
     */
    private $adaptors;

    /**
     * Constructor.
     *
     * @param AdaptorInterface[] $adaptors
     */
    public function __construct(array $adaptors)
    {
        $this->adaptors = $adaptors;
    }

    /**
     * {@inheritdoc}
     *
     * @trows UnexpectedTypeException if $name is not a string
     */
    public function getAdaptor($name)
    {
        if (!is_string($name)) {
            throw new UnexpectedTypeException($name, 'string');
        }

        if ($this->hasAdaptor($name)) {
            return $this->adaptors[$name];
        }

        throw new InvalidArgumentException(sprintf('Could not load adaptor "%s"', $name));
    }

    /**
     * {@inheritdoc}
     *
     * @trows UnexpectedTypeException if $name is not a string
     */
    public function hasAdaptor($name)
    {
        if (!is_string($name)) {
            throw new UnexpectedTypeException($name, 'string');
        }

        if (isset($this->adaptors[$name])) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdaptors()
    {
        return $this->adaptors;
    }
}
