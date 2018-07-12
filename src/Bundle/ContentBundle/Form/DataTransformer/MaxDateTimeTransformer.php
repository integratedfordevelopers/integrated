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

use Integrated\Common\Content\PublishTimeInterface;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class MaxDateTimeTransformer implements DataTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($dateTime)
    {
        if ($dateTime == new \DateTime(PublishTimeInterface::DATE_MAX)) {
            return null; // hide max date
        }

        return $dateTime;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($dateTime)
    {
        return $dateTime;
    }
}
