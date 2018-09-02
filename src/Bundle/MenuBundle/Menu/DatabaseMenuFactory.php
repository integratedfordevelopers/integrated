<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\MenuBundle\Menu;

use Knp\Menu\Factory\CoreExtension;
use Knp\Menu\Factory\ExtensionInterface;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class DatabaseMenuFactory implements FactoryInterface
{
    /**
     * @var string
     */
    protected $menuClass;

    /**
     * @var string
     */
    protected $menuItemClass;

    /**
     * @var array[]
     */
    private $extensions = [];

    /**
     * @var ExtensionInterface[]
     */
    private $sorted;

    /**
     * @param string $menuClass
     * @param string $menuItemClass
     */
    public function __construct($menuClass, $menuItemClass)
    {
        $this->menuClass = $menuClass;
        $this->menuItemClass = $menuItemClass;
        $this->addExtension(new CoreExtension(), -10);
    }

    /**
     * @param string $name
     * @param array  $options
     *
     * @return \Integrated\Bundle\MenuBundle\Document\Menu
     */
    public function createItem($name, array $options = [])
    {
        return $this->getItem($this->menuClass, $name, $options);
    }

    /**
     * @param string $name
     * @param array  $options
     *
     * @return \Integrated\Bundle\MenuBundle\Document\MenuItem
     */
    public function createChild($name, array $options = [])
    {
        return $this->getItem($this->menuItemClass, $name, $options);
    }

    /**
     * @param array $array
     *
     * @return \Integrated\Bundle\MenuBundle\Document\Menu|null
     */
    public function fromArray(array $array = [])
    {
        if (isset($array['name'])) {
            $menu = $this->createItem($array['name']);

            if (isset($value['id'])) {
                $menu->setId($value['id']);
            }

            if (isset($array['children'])) {
                $menu->setChildren($this->parseChildren((array) $array['children']));
            }

            return $menu;
        }
    }

    /**
     * @param array $array
     *
     * @return array
     */
    protected function parseChildren(array $array = [])
    {
        $children = [];

        foreach ($array as $value) {
            if (isset($value['id'])) {
                $child = $this->createChild(isset($value['name']) ? $value['name'] : '');
                $child->setId($value['id']);

                if (isset($value['uri'])) {
                    $child->setUri($value['uri']);
                }

                if (isset($value['children'])) {
                    $child->setChildren($this->parseChildren((array) $value['children']));
                }

                $children[] = $child;
            }
        }

        return $children;
    }

    /**
     * @param $class
     * @param $name
     * @param array $options
     *
     * @return \Integrated\Bundle\MenuBundle\Document\MenuItem
     */
    protected function getItem($class, $name, array $options = [])
    {
        foreach ($this->getExtensions() as $extension) {
            $options = $extension->buildOptions($options);
        }

        $item = new $class($name, $this);

        if (!$item instanceof ItemInterface) {
            throw new \InvalidArgumentException(sprintf('Class "%s" must be an instanceof "ItemInterface".', $class));
        }

        foreach ($this->getExtensions() as $extension) {
            $extension->buildItem($item, $options);
        }

        return $item;
    }

    /**
     * Adds a factory extension.
     *
     * @param ExtensionInterface $extension
     * @param int                $priority
     */
    public function addExtension(ExtensionInterface $extension, $priority = 0)
    {
        $this->extensions[$priority][] = $extension;
        $this->sorted = null;
    }

    /**
     * Sorts the internal list of extensions by priority.
     *
     * @return ExtensionInterface[]
     */
    private function getExtensions()
    {
        if (null === $this->sorted) {
            krsort($this->extensions);
            $this->sorted = !empty($this->extensions) ? \call_user_func_array('array_merge', $this->extensions) : [];
        }

        return $this->sorted;
    }
}
