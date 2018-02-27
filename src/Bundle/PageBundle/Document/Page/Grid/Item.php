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

use Integrated\Bundle\BlockBundle\Document\Block\Block;

/**
 * Item document.
 *
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class Item
{
    /**
     * @var int
     */
    protected $order;

    /**
     * @var Block
     */
    protected $block;

    /**
     * @var Row
     */
    protected $row;

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param int $order
     *
     * @return Item
     */
    public function setOrder($order)
    {
        $this->order = (int) $order;

        return $this;
    }

    /**
     * @return Block
     */
    public function getBlock()
    {
        return $this->block;
    }

    /**
     * @param Block $block
     *
     * @return $this
     */
    public function setBlock(Block $block = null)
    {
        if ($block && $this->row) {
            throw new \RuntimeException('Row is already defined');
        }

        $this->block = $block;

        return $this;
    }

    /**
     * @return Row
     */
    public function getRow()
    {
        return $this->row;
    }

    /**
     * @param Row $row
     *
     * @return $this
     */
    public function setRow(Row $row = null)
    {
        if ($row && $this->block) {
            throw new \RuntimeException('Block is already defined');
        }

        $this->row = $row;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $array = [
            'order' => $this->order,
        ];

        if ($this->block instanceof Block) {
            $array['block'] = $this->block->getId();
        }

        if ($this->row instanceof Row) {
            $array['row'] = $this->row->toArray();
        }

        return $array;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     *
     * @return $this
     */
    public function setAttributes(array $attributes = [])
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasAttribute($key)
    {
        return isset($this->attributes[$key]);
    }

    /**
     * @param string $key
     *
     * @return string|null
     */
    public function getAttribute($key)
    {
        if ($this->hasAttribute($key)) {
            return $this->attributes[$key];
        }

        return null;
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;

        return $this;
    }
}
