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

use Integrated\Common\Normalizer\ContainerFactoryInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ResolvedProcessor implements ResolvedProcessorInterface
{
    /**
     * @var ContainerFactoryInterface
     */
    private $factory;

    /**
     * @var ProcessorInterface[]
     */
    private $processors = [];

    /**
     * The processors will be execute in the order added to the $processors array.
     *
     * @param ProcessorInterface[]      $processors
     * @param ContainerFactoryInterface $factory
     */
    public function __construct(array $processors, ContainerFactoryInterface $factory)
    {
        $this->processors = $processors;
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function process($object, Context $context)
    {
        if (!$this->processors) {
            return [];
        }

        $container = $this->factory->createContainer();

        foreach ($this->processors as $processor) {
            $processor->process($container, $object, $context);
        }

        return $container->toArray();
    }
}
