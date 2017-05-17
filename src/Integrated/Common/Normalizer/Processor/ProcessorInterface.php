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

use Integrated\Common\Normalizer\ContainerInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface ProcessorInterface
{
    /**
     * Extract the data from the $object and add them to the $data container.
     *
     * @param ContainerInterface $data
     * @param object             $object
     * @param Context            $context
     */
    public function process(ContainerInterface $data, $object, Context $context);
}
