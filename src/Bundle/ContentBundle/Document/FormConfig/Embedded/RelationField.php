<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Document\FormConfig\Embedded;

use Integrated\Common\Content\Relation\RelationInterface;

class RelationField extends DocumentField
{
    /**
     * @var RelationInterface
     */
    private $relation;

    /**
     * @return RelationInterface
     */
    public function getRelation(): RelationInterface
    {
        return $this->relation;
    }

    /**
     * @param RelationInterface $relation
     *
     * @return RelationField
     */
    public function setRelation(RelationInterface $relation): self
    {
        $this->relation = $relation;

        return $this;
    }
}
