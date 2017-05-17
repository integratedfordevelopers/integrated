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

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ProcessorRegistryBuilder
{
    /**
     * @var ProcessorInterface[][]
     */
    protected $processors = [];

    protected $processorIndex = [];

    /**
     * @param ProcessorInterface $processor
     * @param string             $class
     */
    public function addProcessor(ProcessorInterface $processor, $class)
    {
        $hash = spl_object_hash($processor);

        if (!isset($this->processorIndex[$hash][$class])) {
            $this->processors[$class][] = $processor;
            $this->processorIndex[$hash][$class] = true;
        }
    }

    /**
     * @return ProcessorRegistry
     */
    public function getRegistry()
    {
        return new ProcessorRegistry($this->processors);
    }
}
