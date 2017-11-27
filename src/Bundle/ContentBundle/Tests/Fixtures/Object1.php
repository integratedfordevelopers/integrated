<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\Fixtures;

use Doctrine\Common\Collections\Collection;
use Integrated\Common\Content\ContentInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Integrated\Common\Content\Embedded\RelationInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Object1 implements ContentInterface
{
    public function getId()
    {
        return 'id1';
    }

    public function getContentType()
    {
        return 'type1';
    }

    public function setContentType($contentType)
    {
        throw new \Exception();
    }

    public function getRelationsByRelationType()
    {
        return new ArrayCollection();
    }

    public function getRelations()
    {
        throw new \Exception();
    }

    public function getRelation($relationId)
    {
        throw new \Exception();
    }

    public function setRelations(Collection $relations)
    {
        throw new \Exception();
    }

    public function addRelation(RelationInterface $relation)
    {
        throw new \Exception();
    }

    public function removeRelation(RelationInterface $relation)
    {
        throw new \Exception();
    }

    public function getSlug()
    {
        throw new \Exception();
    }

    public function setSlug($slug)
    {
        throw new \Exception();
    }
}
