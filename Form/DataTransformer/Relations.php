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
use Doctrine\Common\Util\Debug;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Symfony\Component\Form\DataTransformerInterface;
use Integrated\Common\ContentType\Mapping\Metadata;
use Integrated\Common\ContentType\ContentTypeRelationInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation as Model;

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
     * @var ContentTypeRelationInterface[]
     */
    protected $relations;

    /**
     * @var ObjectManager
     */
    protected $om;

    /**
     * @param ContentTypeRelationInterface[] $relations
     * @param ObjectManager $om
     */
    public function __construct($relations, $om)
    {
        $this->relations = $relations;
        $this->om = $om;
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

                    if ($contentTypeRelation = $this->getContentTypeRelation($relation->getRelationId())) {
                        $references = array();
                        foreach ($relation->getReferences() as $content) {
                            $references[] = $content->getId();
                        }

                        $return[$relation->getRelationId()] = implode(',', $references);
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

                if ($contentTypeRelation = $this->getContentTypeRelation($contentTypeRelation)) {

                    $model = new Model();
                    $model->setRelationId($contentTypeRelation->getId());
                    $model->setRelationType($contentTypeRelation->getType());

                    if (null !== $references) {
                        $references = array_filter(explode(',', $references));
                        foreach ($references as $reference) {

                            if ($content = $this->om->getRepository(self::REPOSITORY)->find($reference)) {
                                $model->addReference($content);
                            }
                        }

                        $relations->add($model);
                    }
                }
            }
        }

        return $relations;
    }

    /**
     * @param string $relationId
     * @return bool|ContentTypeRelationInterface
     */
    protected function getContentTypeRelation($relationId)
    {
        foreach ($this->relations as $relation) {
            if ($relation->getId() == $relationId) {
                return $relation;
            }
        }

        return false;
    }
}