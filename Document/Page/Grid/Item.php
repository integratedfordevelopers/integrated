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

use Integrated\Bundle\BlockBundle\Document\Block\Block;

/**
 * Item document
 *
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 *
 * @ODM\EmbeddedDocument
 */
class Item
{
    /**
     * @var int
     * @ODM\Int
     */
    protected $order;

    /**
     * @var Block
     * @ODM\ReferenceOne(targetDocument="Integrated\Bundle\BlockBundle\Document\Block\Block")
     */
    protected $block;

    /**
     * @var Row
     * @ODM\EmbedOne(targetDocument="Integrated\Bundle\PageBundle\Document\Page\Grid\Row")
     */
    protected $row;

    /**
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param int $order
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
     * @return $this
     */
    public function setBlock(Block $block = null)
    {
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
     * @return $this
     */
    public function setRow(Row $row = null)
    {
        $this->row = $row;
        return $this;
    }
}