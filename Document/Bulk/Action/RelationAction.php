<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Bundle\ContentBundle\Document\Bulk\Action;

use Doctrine\Common\Collections\ArrayCollection;
use Integrated\Bundle\ContentBundle\Bulk\ActionInterface;
use Integrated\Bundle\ContentBundle\Document\Relation\Relation;

/**
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
class RelationAction implements ActionInterface
{
    /**
     * @var string
     */
    protected $name;

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
     */
    public function __construct()
    {
        $this->references = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
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
