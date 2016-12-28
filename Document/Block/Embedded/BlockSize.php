<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Bundle\BlockBundle\Document\Block\Embedded;

use Integrated\Bundle\BlockBundle\Document\Block\Block;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class BlockSize
{
    /**
     * @var Block
     */
    protected $block;

    /**
     * @var int
     */
    protected $size;

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
    public function setBlock(Block $block)
    {
        $this->block = $block;
        return $this;
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
}
