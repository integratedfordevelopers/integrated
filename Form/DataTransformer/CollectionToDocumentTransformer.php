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

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Integrated\Common\Content\ContentInterface;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class CollectionToDocumentTransformer implements DataTransformerInterface
{
    /**
     * @param mixed $value
     * @return mixed|null
     * @throws \Exception
     */
    public function transform($value)
    {
        if (null === $value) {
            return null;
        }
        if ($value instanceof Collection) {
            if ($value->count()) {
                return $value->first();
            }
            return null;
        }
        throw new TransformationFailedException(sprintf('Expected a Collection, "%s" given', gettype($value)));
    }

    /**
     * @param ContentInterface $value
     * @return ArrayCollection
     */
    public function reverseTransform($value)
    {
        if (null !== $value) {
            return new ArrayCollection([$value]);
        }
        return new ArrayCollection([]);
    }
}