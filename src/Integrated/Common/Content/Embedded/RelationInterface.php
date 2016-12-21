<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content\Embedded;

use Doctrine\Common\Collections\ArrayCollection;

use Integrated\Common\Content\ContentInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
interface RelationInterface
{
    /**
     * @return string
     */
    public function getRelationId();

    /**
     * @return string
     */
    public function getRelationType();

    /**
     * Get references of Relation
     *
     * @return ContentInterface[]|ArrayCollection
     */
    public function getReferences();
}
