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
 * @author Björn Borneman <bjorn@e-active.nl>
 */
class DateTime implements DataTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($datetime)
    {
        if ($datetime instanceof \DateTimeInterface) {
            if ($datetime->getTimestamp() <= 0) {
                return null;
            }
            return $datetime->format('d-m-Y H:i');
        }

        if (null !== $datetime && '' !== $datetime) {
            throw new TransformationFailedException('Expected datetime or null');
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($string)
    {
        if (null !== $string && '' !== $string) {
            if ($object = \DateTime::createFromFormat('d-m-Y H:i', $string)) {
                return $object;
            }

            throw new TransformationFailedException('No valid date string, should be "d-m-Y H:i"');
        }

        return null;
    }
}
