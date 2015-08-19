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

use Integrated\Bundle\MenuBundle\Menu\DatabaseMenuFactory;

use Knp\Menu\ItemInterface;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class Menu extends MenuItem
{
    /**
     * {@inheritdoc}
     */
    public function addChild($child, array $options = [])
    {
        if ($child instanceof Menu) {
            throw new \InvalidArgumentException('Cannot add an instance of "Menu" as child, use "MenuItem" instead.');
        }

        if (!$child instanceof ItemInterface) {
            if ($this->factory instanceof DatabaseMenuFactory) {
                $child = $this->factory->createChild($child, $options);
            } else {
                $child = $this->factory->createItem($child, $options);
            }
        } elseif (null !== $child->getParent()) {
            throw new \InvalidArgumentException('Cannot add menu item as child, it already belongs to another menu (e.g. has a parent).');
        }

        $child->setParent($this);

        $this->children[$child->getName()] = $child;

        return $child;
    }
}
