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
interface ResolvedProcessorFactoryInterface
{
    /**
     * Create a resolved processor from the given processors.
     *
     * @param array $processors
     * @return ResolvedProcessorInterface
     */
    public function createProcessor(array $processors);
}
