<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\MenuBundle\Document;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class Menu extends MenuItem
{
    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $children = [];

        /** @var MenuItem $child */
        foreach ($this->children as $child) {
            $children[] = $child->toArray();
        }

        return [
            'id'       => $this->getId(),
            'children' => $children,
        ];
    }
}
