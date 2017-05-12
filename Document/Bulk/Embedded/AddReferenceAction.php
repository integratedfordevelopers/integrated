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

use Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation as EmbeddedRelation;
use Integrated\Bundle\ContentBundle\Document\Relation\Relation;

/**
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
class AddReferenceAction extends RelationAction
{
    /**
     * @var string
     */
    protected $actionType = self::class;

    /**
     * @var string
     */
    protected $typeOfAction = "add";

    /**
     * AddReferenceAction constructor.
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
     * @param Relation $relation
     * @param Content $content
     * @return bool
     */
    protected function checkRelCon(Relation $relation, Content $content)
    {
        return $relation->getSources()->exists(function ($key, $element) use ($content) {
            return $element instanceof ContentType && $element->getId() === $content->getContentType();
        });
    }
}
