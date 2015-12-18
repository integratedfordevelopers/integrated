<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Form\DataTransformer;

use Integrated\Bundle\StorageBundle\Document\Embedded\StorageInterface;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * This class acts as a sanity check for the form.
 *
 * @author Johnny Borg <johnny@e-active.nl>
 */
class FileDataTransformer implements DataTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (($value instanceof StorageInterface) || $value == null) {
            return $value;
        }

        throw new TransformationFailedException(
            sprintf(
                'Class %s given while a instance of Integrated\Bundle\StorageBundle\Document\Embedded\StorageInterface was excepted',
                is_object($value) ? get_class($value) : gettype($value)
            )
        );
    }
}
