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
use Integrated\Common\Content\ContentInterface;

/**
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
class RemoveReferenceActionHandler implements ActionHandlerInterface
{
    /**
     * @param ContentInterface $content
     * @return $this
     */
    public function execute(ContentInterface $content, array $options){
        // TODO make sure this works with option instead of $this->attributes
        if ($embeddedRelation = $content->getRelation($this->relation->getId())) {
            if ($embeddedRelation instanceof EmbeddedRelation) {
                foreach ($this->getReferences() as $reference) {
                    $embeddedRelation->removeReference($reference);
                }
                if (count($embeddedRelation->getReferences()) == 0) {
                    $content->removeRelation($embeddedRelation);
                }
            }
        }

        return $this;
    }
}