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
interface ResolverInterface
{
    /**
     * Get the processor for the given object or class.
     *
     * @param object|string $object
     *
     * @return ResolvedProcessorInterface
     */
    public function getProcessor($object);
}
