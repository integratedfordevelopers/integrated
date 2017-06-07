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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Integrated\Bundle\ContentBundle\Document\Bulk\Action\RelationAction;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ActionsTransformer implements DataTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if ($value instanceof Collection) {
            return $this->filterCollection($value);
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if ($value instanceof Collection) {
            $value = $this->filterCollection($value)->toArray();
        }

        if (is_array($value)) {
            $value = array_values(array_filter($value));
        }

        return new ArrayCollection($value);
    }

    /**
     * @param Collection $collection
     * @return Collection
     */
    protected function filterCollection(Collection $collection)
    {
        return $collection->filter(function ($relation) {
            if ($relation instanceof RelationAction) {
                return $relation->getReferences()->count() > 0;
            }

            return false;
        });
    }
}
