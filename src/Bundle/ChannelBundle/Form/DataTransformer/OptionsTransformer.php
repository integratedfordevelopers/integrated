<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ChannelBundle\Form\DataTransformer;

use Integrated\Common\Channel\Connector\Config\Options;
use Integrated\Common\Channel\Connector\Config\OptionsInterface;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class OptionsTransformer implements DataTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        $result = [];

        if ($value instanceof OptionsInterface) {
            $result = $value->toArray();
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (!\is_array($value)) {
            $value = [];
        }

        return new Options($value);
    }
}
