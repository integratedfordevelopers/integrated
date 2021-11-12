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
use Doctrine\Persistence\ObjectManager;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation as EmbeddedRelation;
use Integrated\Common\Content\Relation\RelationInterface;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class Relations implements DataTransformerInterface
{
    /**
     * @var string
     */
    const REPOSITORY = 'Integrated\Bundle\ContentBundle\Document\Content\Content';

    /**
     * @var RelationInterface[]
     */
    protected $relations;

    /**
     * @var ObjectManager
     */
    protected $om;

    /**
     * @param RelationInterface[] $relations
     * @param ObjectManager       $om
     */
    public function __construct($relations, ObjectManager $om)
    {
        $this->relations = $relations;
        $this->om = $om;
    }

    /**
     * Transform an array with EmbeddedRelation to an array with relations containing comma separated references.
     *
     * {@inheritdoc}
     */
    public function transform($value)
    {
        $return = [];
        if (\is_array($value) || $value instanceof \Traversable) {
            foreach ($value as $embeddedRelation) {
                if ($embeddedRelation instanceof EmbeddedRelation) {
                    if ($relation = $this->getRelation($embeddedRelation->getRelationId())) {
                        $references = [];
                        foreach ($embeddedRelation->getReferences() as $content) {
                            $references[] = $content->getId();
                        }

                        $return[$relation->getId()] = implode(',', $references);
                    }
                }
            }
        }

        return $return;
    }

    /**
     * Transform an array with relations containing comma separated references to an ArrayCollection with
     * EmbeddedRelation.
     *
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        $relations = new ArrayCollection();

        if (\is_array($value)) {
            foreach ($value as $relationId => $references) {
                if ($relation = $this->getRelation($relationId)) {
                    $embeddedRelation = new EmbeddedRelation();
                    $embeddedRelation->setRelationId($relation->getId());
                    $embeddedRelation->setRelationType($relation->getType());

                    if (null !== $references) {
                        $references = array_filter(explode(',', $references));
                        foreach ($references as $reference) {
                            if ($content = $this->om->getRepository(self::REPOSITORY)->find($reference)) {
                                $embeddedRelation->addReference($content);
                            }
                        }

                        $relations->add($embeddedRelation);
                    }
                }
            }
        }

        return $relations;
    }

    /**
     * @param string $relationId
     *
     * @return bool|RelationInterface
     */
    protected function getRelation($relationId)
    {
        foreach ($this->relations as $relation) {
            if ($relation->getId() == $relationId) {
                return $relation;
            }
        }

        return false;
    }
}
