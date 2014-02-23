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
        if (empty($datetime)) {
            return "";
        }

        return $datetime->format('d-m-Y - H:i');
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($string)
    {
        $object = \DateTime::createFromFormat('d-m-Y - H:i',$string);
        if (!$object) {
            return null;
        }

        return $object;
    }
}