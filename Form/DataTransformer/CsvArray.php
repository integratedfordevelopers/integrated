<?php
/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\File;

/**
 * DataTransformer which handles comma separated values and return an array
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class CsvArray implements DataTransformerInterface
{
    /**
     * @param mixed $value
     * @return mixed
     */
    public function transform($value)
    {
        if (empty($value)) {
            $value = array();
        }

        return implode(', ', $value);
    }

    /**
     * @param mixed $value
     * @return array
     */
    public function reverseTransform($value)
    {
        if (empty($value)) {
            $value = '';
        }

        return array_filter(array_map('trim', explode(',', $value)));
    }
}