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

use Doctrine\Common\Collections\Collection;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation as EmbeddedRelation;
use Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;
use Integrated\Common\Content\ContentInterface;
use Integrated\Common\Content\Relation\RelationInterface;

/**
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
class AddReferenceActionHandler extends RelationActionHandler
{
    /**
     * @param ContentInterface $content
     * @param array $options
     * @return $this
     */
    public function execute(ContentInterface $content, array $options){
        $this->validateOptions($options);

        if ($embeddedRelation = $content->getRelation($options['relation']->getId())) {
            if ($embeddedRelation instanceof EmbeddedRelation) {
                $embeddedRelation->addReferences($options['references']);
            }
        } elseif ($this->checkRelCon($options['relation'], $content)) {
            $embeddedRelation = new EmbeddedRelation();
            $embeddedRelation->setRelationId($options['relation']->getId());
            $embeddedRelation->setRelationType($options['relation']->getType());
            $embeddedRelation->addReferences($options['references']);

            $content->addRelation($embeddedRelation);
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
        $sources = $relation->getSources();

        /* @var Collection $sources */
        return $sources->exists(function ($key, $element) use ($content) {
            return $element instanceof ContentType && $element->getId() === $content->getContentType();
        });
    }
}