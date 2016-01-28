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

use Doctrine\Common\Collections\ArrayCollection;
use Integrated\Common\Content\ContentInterface;
use Symfony\Component\Form\DataTransformerInterface;

use Doctrine\Common\Collections\Collection;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class CollectionToDocumentTransformer implements DataTransformerInterface
{
    /**
     * @param Collection $value
     * @return ContentInterface|null
     */
    public function transform($value)
    {
        if ($value instanceof Collection) {
            return $value->first();
        }
    }

    /**
     * @param ContentInterface $value
     * @return ArrayCollection
     */
    public function reverseTransform($value)
    {
        if ($value) {
            return new ArrayCollection([$value]);
        }
        return new ArrayCollection([]);
    }
}