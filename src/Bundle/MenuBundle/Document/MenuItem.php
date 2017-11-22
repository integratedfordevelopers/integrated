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
use Knp\Menu\ItemInterface;
use Knp\Menu\FactoryInterface;

use Doctrine\Common\Collections\Collection;

use Integrated\Bundle\MenuBundle\Menu\DatabaseMenuFactory;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class MenuItem extends KnpMenuItem
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @param string $name
     * @param DatabaseMenuFactory $factory
     */
    public function __construct($name, DatabaseMenuFactory $factory)
    {
        $this->name = (string) $name;
        $this->factory = $factory;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param FactoryInterface $factory
     * @return $this
     */
    public function setFactory(FactoryInterface $factory)
    {
        if (!$factory instanceof DatabaseMenuFactory) {
            throw new \InvalidArgumentException(
                'Factory must be an instance of "Integrated\Bundle\MenuBundle\Menu\DatabaseMenuFactory".'
            );
        }

        $this->factory = $factory;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addChild($child, array $options = [])
    {
        if ($child instanceof Menu) {
            throw new \InvalidArgumentException(
                'Cannot add an instance of "Integrated\Bundle\MenuBundle\Document\Menu" as child, ' .
                'use "Integrated\Bundle\MenuBundle\Document\MenuItem" instead.'
            );
        }

        if (!$child instanceof ItemInterface) {
            $child = $this->factory->createChild($child, $options);

        } elseif (null !== $child->getParent()) {
            throw new \InvalidArgumentException(
                'Cannot add menu item as child, it already belongs to another menu (e.g. has a parent).'
            );
        }

        $child->setParent($this);
        $this->children[$child->getId()] = $child;

        return $child;
    }

    /**
     * {@inheritdoc}
     */
    public function getChild($id)
    {
        /** @var MenuItem $child */
        foreach ($this->children as $child) {
            if ($child->getId() === $id) {
                return $child;
            }
        }
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

    /**
     * @return \Knp\Menu\ItemInterface
     */
    public function getFirstChild()
    {
        $children = $this->getChildren();

        return reset($children);
    }

    /**
     * @return \Knp\Menu\ItemInterface
     */
    public function getLastChild()
    {
        $children = $this->getChildren();

        return end($children);
    }

    /**
     * @param bool $nested
     * @return array
     */
    public function toArray($nested = true)
    {
        $array = [];

        if ($this->getId()) {
            $array['id'] = $this->getId();
        }

        if ($this->getName()) {
            $array['name'] = $this->getName();
        }

        if ($this->getUri()) {
            $array['uri'] = $this->getUri();
        }

        if (true === $nested) {
            $children = [];

            /** @var MenuItem $child */
            foreach ($this->children as $child) {
                $children[] = $child->toArray($nested);
            }

            if (count($children)) {
                $array['children'] = $children;
            }
        }

        return $array;
    }
}
