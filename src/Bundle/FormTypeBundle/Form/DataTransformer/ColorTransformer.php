<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\FormTypeBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ColorTransformer implements DataTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        $value = strtolower(trim($value));

        if ($value && $value[0] === '#') {
            $value = substr($value, 1);
        }

        if (\strlen($value) == 6 && ctype_xdigit($value)) {
            return '#'.$value;
        }

        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (null === $value) {
            return '';
        }

        if (!\is_string($value)) {
            throw new TransformationFailedException('Expected a string.');
        }

        $value = strtolower(trim($value));

        if (!$value) {
            return '';
        }

        if (\strlen($value) == 7 && $value[0] === '#' && ctype_xdigit(substr($value, 1))) {
            return $value;
        }

        throw new TransformationFailedException(sprintf('The value %s is not a valid hexadecimal color string.', $value));
    }
}
