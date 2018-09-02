<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Normalizer\Processor;

use Integrated\Common\Normalizer\Exception\InvalidArgumentException;
use Integrated\Common\Normalizer\Exception\UnexpectedTypeException;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ProcessorRegistry implements RegistryInterface
{
    /**
     * @var ProcessorInterface[][]
     */
    private $processors;

    /**
     * @param ProcessorInterface[][] $processors
     */
    public function __construct(array $processors)
    {
        $this->processors = $processors;
    }

    /**
     * {@inheritdoc}
     */
    public function hasProcessors($class)
    {
        if (!\is_string($class)) {
            throw new UnexpectedTypeException($class, 'string');
        }

        if (isset($this->processors[$class])) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getProcessors($class)
    {
        if (!\is_string($class)) {
            throw new UnexpectedTypeException($class, 'string');
        }

        if (isset($this->processors[$class])) {
            return $this->processors[$class];
        }

        throw new InvalidArgumentException(sprintf('No processors found for class "%s"', $class));
    }
}
