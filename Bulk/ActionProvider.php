<?php
/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Bulk;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\ContentBundle\Document\Bulk\Embedded\AddReferenceAction;
use Integrated\Bundle\ContentBundle\Document\Bulk\Embedded\RelationAction;
use Integrated\Bundle\ContentBundle\Document\Bulk\Embedded\RemoveReferenceAction;
use Integrated\Bundle\ContentBundle\Document\Relation\Relation;

/**
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
class ActionProvider
{
    /**
     * @var DocumentManager
     */
    private $dm;

    /**
     * ActionProvider constructor.
     * @param DocumentManager $dm
     */
    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    /**
     * @param Collection|null $arrayCollection
     * @return ArrayCollection
     */
    public function getActions(Collection $arrayCollection = null)
    {
        $actions = new ArrayCollection();

        //Add AddReferenceAction and RemoveReferenceAction for each relation
        foreach ($this->dm->getRepository(Relation::class)->findAll() as $relation) {
            if (!$arrayCollection || !$this->checkRelActionInCollection($arrayCollection, $relation, AddReferenceAction::class)) {
                $actions->add(new AddReferenceAction($relation));
            }

            if (!$arrayCollection || !$this->checkRelActionInCollection($arrayCollection, $relation, RemoveReferenceAction::class)) {
                $actions->add(new RemoveReferenceAction($relation));
            }
        }

        return $actions;
    }

    /**
     * @param Collection $arrayCollection
     * @param Relation $relation
     * @param string $actionClass
     * @return bool
     */
    protected function checkRelActionInCollection(Collection $arrayCollection, Relation $relation, $actionClass)
    {
        return $arrayCollection->exists(function ($key, $element) use ($relation, $actionClass) {
            return $element instanceof $actionClass && $element instanceof RelationAction && $element->getRelation()->getId() == $relation->getId();
        });
    }
}
