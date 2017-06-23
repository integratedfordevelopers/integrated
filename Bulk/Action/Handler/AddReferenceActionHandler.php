<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Bulk\Action\Handler;

use Doctrine\Common\Collections\Collection;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation as EmbeddedRelation;
use Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;
use Integrated\Common\Content\ContentInterface;
use Integrated\Common\Content\Relation\RelationInterface;

/**
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
class AddReferenceActionHandler extends AbstractRelationActionHandler
{
    /**
     * @param ContentInterface $content
     * @param array $options
     * @return void
     */
    public function execute(ContentInterface $content, array $options)
    {
        $options = $this->validateOptions($options);

        if ($embeddedRelation = $content->getRelation($options['relation']->getId())) {
            if ($embeddedRelation instanceof EmbeddedRelation) {
                $embeddedRelation->addReferences($options['references']);
            }
        } elseif ($this->checkRelationContent($options['relation'], $content)) {
            $embeddedRelation = new EmbeddedRelation();
            $embeddedRelation->setRelationId($options['relation']->getId());
            $embeddedRelation->setRelationType($options['relation']->getType());
            $embeddedRelation->addReferences($options['references']);

            $content->addRelation($embeddedRelation);
        }
    }

    /**
     * @param RelationInterface $relation
     * @param ContentInterface $content
     * @return bool
     */
    private function checkRelationContent(RelationInterface $relation, ContentInterface $content)
    {
        $sources = $relation->getSources();

        /* @var Collection $sources */
        return $sources->exists(function ($key, $element) use ($content) {
            return $element instanceof ContentType && $element->getId() === $content->getContentType();
        });
    }
}
