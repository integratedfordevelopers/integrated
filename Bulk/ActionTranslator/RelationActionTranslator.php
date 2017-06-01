<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Bulk\ActionTranslator;

use Integrated\Bundle\ContentBundle\Document\Content\Article;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\ContentBundle\Document\Content\File;
use Integrated\Bundle\ContentBundle\Document\Content\Relation\Company;
use Integrated\Bundle\ContentBundle\Document\Content\Relation\Person;
use Integrated\Bundle\ContentBundle\Document\Content\Taxonomy;
use Integrated\Bundle\ContentBundle\Document\Relation\Relation;

/**
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
class RelationActionTranslator extends ActionTranslator
{
    /**
     * @return string
     */
    public function getKindOfAction()
    {
        switch ($this->action->getName()) {
            case "AddReferenceActionHandler":
                return "add";
            case "RemoveReferenceActionHandler":
                return "remove";
            default:
                return "";
        }
    }

    /**
     * @return string
     */
    public function getTarget()
    {
        /* @var Relation $relation */
        $relation = $this->getActionOption('relation');

        if($relation instanceof Relation){
            return $relation->getName();
        }

        return "";
    }

    /**
     * @return array
     */
    public function getChanges()
    {
        $changeNames = [];

        foreach ($this->getActionOption('references') as $reference) {
            if ($reference instanceof Content) {
                if ($reference instanceof Article || $reference instanceof Taxonomy || $reference instanceof File) {
                    $changeNames[] = $reference->getTitle();
                } elseif ($reference instanceof Person) {
                    $changeNames[] = $reference->getFirstName() . " " . $reference->getLastName();
                } elseif ($reference instanceof Company) {
                    $changeNames[] = $reference->getName();
                } else {
                    $changeNames[] = $reference->getId();
                }
            }
        }

        return $changeNames;
    }
}