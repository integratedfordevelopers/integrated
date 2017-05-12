<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Bundle\ContentBundle\Document\Bulk\Embedded;

use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation as EmbeddedRelation;
use Integrated\Bundle\ContentBundle\Document\Relation\Relation;

/**
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
class RemoveReferenceAction extends RelationAction
{
    /**
     * @var string
     */
    protected $actionType = self::class;

    /**
     * @var string
     */
    protected $typeOfAction = "remove";

    /**
     * RemoveReferenceAction constructor.
     * @param Relation $relation
     */
    public function __construct(Relation $relation)
    {
        parent::__construct($relation);
    }

    /**
     * @return string
     */
    public function getTypeOfAction()
    {
        return $this->typeOfAction;
    }

    /**
     * @param Content $content
     * @return $this
     */
    public function execute(Content $content)
    {
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
