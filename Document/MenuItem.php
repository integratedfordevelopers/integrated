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

use Knp\Menu\MenuItem as KnpMenuItem;

use Doctrine\Common\Collections\Collection;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class MenuItem extends KnpMenuItem
{
    /**
     * {@inheritdoc}
     */
    public function addChild($child, array $options = [])
    {
        if ($child instanceof Menu) {
            throw new \InvalidArgumentException('Cannot add an instance of "Menu" as child, use "MenuItem" instead.');
        }

        parent::addChild($child, $options);
    }

    /**
     * @return \Knp\Menu\ItemInterface[]
     */
    public function getChildren()
    {
        if ($this->children instanceof Collection) {
            return $this->children->toArray();
        }

        return $this->children;
    }
}
