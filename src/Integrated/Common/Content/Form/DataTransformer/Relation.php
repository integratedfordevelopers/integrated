<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Util\Debug;
use Symfony\Component\Form\DataTransformerInterface;
use Integrated\Common\ContentType\Mapping\Metadata;
use Integrated\Common\ContentType\ContentTypeRelationInterface;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class Relation implements DataTransformerInterface
{
    /**
     * @var ContentTypeRelationInterface[]
     */
    protected $relations;

    /**
     * @param ContentTypeRelationInterface[] $relations
     */
    public function __construct($relations)
    {
        $this->relations = $relations;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        $return = array();
        if (is_array($value) || $value instanceof \Traversable) {
            foreach ($value as $relation) {

                if ($relation instanceof \Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation) {

                    foreach ($this->relations as $contentTypeRelation) {
                        if ($contentTypeRelation->getId() == $relation->getContentTypeRelation()) {
                            if ($contentTypeRelation->getMultiple()) {
                                $return[$relation->getContentTypeRelation()] = $relation->getReferences();
                            } else {
                                $return[$relation->getContentTypeRelation()] = $relation->getReferences()->first();
                            }
                        }
                    }
                }
            }
        }

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        $relations = new ArrayCollection();
        if (is_array($value)) {
            foreach ($value as $contentTypeRelation => $references) {

                // TODO remove ContentBundle dependency
                $relation = new \Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation();
                $relation->setContentTypeRelation($contentTypeRelation);

                if (is_array($references) || $references instanceof \Traversable) {
                    /** @var $item  */
                    foreach ($references as $reference) {
                        $relation->addReference($reference);
                    }
                } else {
                    $relation->addReference($references);
                }

                $relations->add($relation);
            }
        }

        return $relations;
    }
}