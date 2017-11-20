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
interface ResolvedProcessorInterface
{
    /**
     * Run the internal processors over the object to transform it into a array.
     *
     * @param object  $object
     * @param Context $context
     *
     * @return array
     */
    public function process($object, Context $context);
}
