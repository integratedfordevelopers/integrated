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
use Integrated\Bundle\ContentBundle\Document\Content\Embedded as Embedded;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class Translatable implements DataTransformerInterface
{
    /**
     * @param mixed $relation
     *
     * @return array|mixed
     */
    public function transform($relation)
    {
        if ($relation instanceof Embedded\Translatable) {
            return $relation->getTranslations();
        }

        return [];
    }

    /**
     * @param mixed $value
     *
     * @return Embedded\Translatable|null
     */
    public function reverseTransform($value)
    {
        if (is_array($value)) {
            $translatable = new Embedded\Translatable();
            $translatable->setTranslations($value);

            return $translatable;
        }

        return null;
    }
}
