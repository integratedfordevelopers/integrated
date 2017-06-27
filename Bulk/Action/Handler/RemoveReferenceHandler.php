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

use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation as EmbeddedRelation;
use Integrated\Common\Content\ContentInterface;

/**
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
class RemoveReferenceHandler extends AbstractRelationHandler
{
    /**
     * @param ContentInterface $content
     * @param array $options
     * @return void
     */
    public function execute(ContentInterface $content, array $options)
    {
        $this->validateOptions($options);

        if ($embeddedRelation = $content->getRelation($options['relation']->getId())) {
            if ($embeddedRelation instanceof EmbeddedRelation) {
                foreach ($options['references'] as $reference) {
                    $embeddedRelation->removeReference($reference);
                }
                if (count($embeddedRelation->getReferences()) == 0) {
                    $content->removeRelation($embeddedRelation);
                }
            }
        }
    }
}
