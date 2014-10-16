<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Converter\Type;

use Integrated\Common\Converter\Exception\InvalidArgumentException;
use Integrated\Common\Converter\Exception\UnexpectedTypeException;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Registry implements RegistryInterface
{
    /**
     * @var ResolvedTypeInterface[]
     */
    private $resolved = [];

    /**
     * @param ResolvedTypeInterface[] $resolved
     */
    public function __construct(array $resolved)
    {
        $this->resolved = $resolved;
    }

    /**
     * {@inheritdoc}
     */
    public function hasType($name)
    {
        if (!is_string($name)) {
            throw new UnexpectedTypeException($name, 'string');
        }

        if (isset($this->resolved[$name])) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getType($name)
    {
        if (!is_string($name)) {
            throw new UnexpectedTypeException($name, 'string');
        }

        if ($this->hasType($name)) {
            return $this->resolved[$name];
        }

        throw new InvalidArgumentException(sprintf('Could not load converter type "%s"', $name));
    }
}
