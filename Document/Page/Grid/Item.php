<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Bundle\WebsiteBundle\Document\Page\Grid;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

use Integrated\Bundle\WebsiteBundle\Document\Block\Block;

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
     * @var Block
     * @ODM\ReferenceOne(targetDocument="Integrated\Bundle\WebsiteBundle\Document\Block\Block")
     */
    protected $block;

    /**
     * @var Row
     * @ODM\EmbedOne(targetDocument="Integrated\Bundle\WebsiteBundle\Document\Page\Grid\Row")
     */
    protected $row;

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