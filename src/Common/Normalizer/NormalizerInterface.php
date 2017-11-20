<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Normalizer;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface NormalizerInterface
{
    /**
     * Normalize the $object to a array.
     *
     * @param object $object
     * @param array  $options
     *
     * @return array
     */
    public function normalize($object, array $options = []);
}
