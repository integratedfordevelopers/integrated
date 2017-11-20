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
interface RegistryInterface
{
    /**
     * Check if there is a processors for the $class in the registry
     *
     * @param string $class
     * @return bool
     */
    public function hasProcessors($class);

    /**
     * Get the processors for the $class from the registry
     *
     * @param string $class
     * @return ProcessorInterface[]
     *
     * @trows InvalidArgumentException if the processors can not be found
     */
    public function getProcessors($class);
}
