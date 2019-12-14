<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Block;

use Integrated\Bundle\ContentBundle\Document\Relation\Relation;
use Integrated\Common\Content\ContentInterface;

interface BlockRequiredItemsInterface
{
    /**
     * Get the (optional) required relation.
     *
     * @return Relation|null
     */
    public function getRequiredRelation();

    /**
     * Get the (optional) required items.
     *
     * @return ContentInterface[]
     */
    public function getRequiredItems();
}
