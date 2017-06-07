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

use Integrated\Bundle\ContentBundle\Document\Bulk\Action\RelationAction;
use Integrated\Bundle\ContentBundle\Document\Relation\Relation;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class RelationActionTransformer implements DataTransformerInterface
{
    /**
     * @var Relation
     */
    protected $relation;

    /**
     * @var string
     */
    protected $name;

    /**
     * @param Relation $relation
     * @param string $name
     */
    public function __construct(Relation $relation, $name)
    {
        $this->relation = $relation;
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (!$value instanceof RelationAction) {
            return new RelationAction($this->relation);
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if ($value instanceof RelationAction) {
            if (!$value->getReferences()->count()) {
                return null;
            }

            $value
                ->setRelation($this->relation)
                ->setName($this->name)
            ;
        }

        return $value;
    }
}
