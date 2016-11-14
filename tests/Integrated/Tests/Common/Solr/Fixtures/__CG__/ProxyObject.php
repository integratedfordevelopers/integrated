<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\Solr\Fixtures\__CG__;

use Doctrine\Common\Collections\Collection;

use Integrated\Common\Content\ContentInterface;
use Integrated\Common\Content\Relation\RelationInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 *
 * @codeCoverageIgnore
 */
class ProxyObject implements ContentInterface
{
    public function getId()
    {
        return 'proxy-id';
    }

    public function getContentType()
    {
        return 'proxy-type';
    }

    public function setContentType($contentType)
    {
        throw new \Exception();
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
}
