<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Bundle\ContentBundle\Document\Bulk;

use Doctrine\Common\Collections\ArrayCollection;
use Integrated\Bundle\ContentBundle\Document\Relation\Relation;

/**
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
class RelationAction extends Action
{
    /**
     * @var Relation
     */
    protected $relation;

    /**
     * @var ArrayCollection contains ContentInterface
     */
    protected $references;

    /**
     * RelationAction constructor.
     * @param Relation $relation
     */
    public function __construct()
    {
        $this->references = new ArrayCollection();
    }

    /**
     * @return Relation
     */
    public function getRelation()
    {
        return $this->relation;
    }

    /**
     * @param Relation $relation
     * @return $this
     */
    public function setRelation(Relation $relation)
    {
        $this->relation = $relation;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getReferences()
    {
        return $this->references;
    }

    /**
     * @param ArrayCollection $references
     * @return $this
     */
    public function setReferences(ArrayCollection $references)
    {
        $this->references = $references;
        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return [
            'relation' => $this->getRelation(),
            'references' =>$this->getReferences()
        ];
    }
}