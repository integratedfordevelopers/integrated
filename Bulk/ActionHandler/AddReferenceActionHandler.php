<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Bulk\ActionHandler;

use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation as EmbeddedRelation;
use Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;
use Integrated\Common\Content\ContentInterface;
use Integrated\Common\Content\Relation\RelationInterface;

/**
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
class AddReferenceActionHandler implements ActionHandlerInterface
{
    /**
     * @param ContentInterface $content
     * @param array $options
     * @return $this
     */
    public function execute(ContentInterface $content, array $options){
        // TODO make sure this works with option instead of $this->attributes
        if ($embeddedRelation = $content->getRelation($this->relation->getId())) {
            if ($embeddedRelation instanceof EmbeddedRelation) {
                $embeddedRelation->addReferences($this->references);
            }
        } elseif ($relation = $this->getRelation()) {
            if ($this->checkRelCon($relation, $content)) {
                $embeddedRelation = new EmbeddedRelation();
                $embeddedRelation->setRelationId($relation->getId());
                $embeddedRelation->setRelationType($relation->getType());
                $embeddedRelation->addReferences($this->references);

                $content->addRelation($embeddedRelation);
            }
        } else {
            throw new \RuntimeException('No Relation could be fetched.');
        }

        return $this;
    }

    /**
     * @param RelationInterface $relation
     * @param ContentInterface $content
     * @return bool
     */
    private function checkRelCon(RelationInterface $relation, ContentInterface $content)
    {
        return $relation->getSources()->exists(function ($key, $element) use ($content) {
            return $element instanceof ContentType && $element->getId() === $content->getContentType();
        });
    }
}