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

use Integrated\Common\Normalizer\ContainerFactory;
use Integrated\Common\Normalizer\ContainerFactoryInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ResolvedProcessorFactory implements ResolvedProcessorFactoryInterface
{
    /**
     * @var ContainerFactoryInterface
     */
    private $factory;

    /**
     * @param ContainerFactoryInterface $factory
     */
    public function __construct(ContainerFactoryInterface $factory = null)
    {
        $this->factory = $factory ?: new ContainerFactory();
    }

    /**
     * {@inheritdoc}
     */
    public function createProcessor(array $processors)
    {
        return new ResolvedProcessor($processors, $this->factory);
    }
}
