<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\PageBundle\Document\Page\Grid;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Column document
 *
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 *
 * @ODM\EmbeddedDocument
 */
class Column
{
    /**
     * @var int
     * @ODM\Int
     */
    protected $size;

    /**
     * @var array
     * @ODM\EmbedMany(targetDocument="Integrated\Bundle\PageBundle\Document\Page\Grid\Item")
     */
    protected $items;

    /**
     */
    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param int $size
     * @return $this
     */
    public function setSize($size)
    {
        $this->size = (int) $size;
        return $this;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items->toArray();
    }

    /**
     * @param array $items
     * @return $this
     */
    public function setItems(array $items)
    {
        $this->items = new ArrayCollection($items);
        return $this;
    }

    /**
     * @param Item $item
     * @return $this
     */
    public function addItem(Item $item)
    {
        $this->items->add($item);
        return $this;
    }

    /**
     * @param Item $item
     * @return $this
     */
    public function removeItem(Item $item)
    {
        $this->items->removeElement($item);
        return $this;
    }
}
