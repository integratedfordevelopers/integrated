<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\DataFixtures\MongoDB\Extension;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
trait ArrayCollectionExtension
{
    /**
     * @param array $elements
     *
     * @return ArrayCollection
     */
    public function arrayCollection(array $elements)
    {
        return new ArrayCollection($elements);
    }
}
