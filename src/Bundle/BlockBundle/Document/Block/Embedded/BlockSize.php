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
     * @deprecated
     *
     * @var Block
     */
    protected $block;

    /**
     * @var int
     */
    protected $size;

    /**
     * @var int
     */
    protected $sizeXs;

    /**
     * @var int
     */
    protected $sizeSm;

    /**
     * @var int
     */
    protected $sizeMd;

    /**
     * @var int
     */
    protected $sizeLg;

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
    public function setBlock(Block $block)
    {
        $this->block = $block;

        return $this;
    }

    /**
     * @return int
     *
     * @deprecated use sizeXs, sizeMd etc instead
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param int $size
     *
     * @deprecated use sizeXs, sizeMd etc instead
     *
     * @return $this
     */
    public function setSize($size)
    {
        $this->size = (int) $size;

        return $this;
    }

    /**
     * @return int
     */
    public function getSizeXs()
    {
        return $this->sizeXs;
    }

    /**
     * @param int $sizeXs
     *
     * @return $this
     */
    public function setSizeXs($sizeXs)
    {
        $this->sizeXs = $sizeXs;

        return $this;
    }

    /**
     * @return int
     */
    public function getSizeSm()
    {
        return $this->sizeSm;
    }

    /**
     * @param int $sizeSm
     *
     * @return $this
     */
    public function setSizeSm($sizeSm)
    {
        $this->sizeSm = $sizeSm;

        return $this;
    }

    /**
     * @return int
     */
    public function getSizeMd()
    {
        return $this->sizeMd;
    }

    /**
     * @param int $sizeMd
     *
     * @return $this
     */
    public function setSizeMd($sizeMd)
    {
        $this->sizeMd = $sizeMd;

        return $this;
    }

    /**
     * @return int
     */
    public function getSizeLg()
    {
        return $this->sizeLg;
    }

    /**
     * @param int $sizeLg
     *
     * @return $this
     */
    public function setSizeLg($sizeLg)
    {
        $this->sizeLg = $sizeLg;

        return $this;
    }

    /**
     * @return array
     */
    public function getSizes()
    {
        $sizes = [];
        if ($this->getSizeXs()) {
            $sizes['xs'] = $this->getSizeXs();
        }
        if ($this->getSizeSm()) {
            $sizes['sm'] = $this->getSizeSm();
        }
        if ($this->getSizeMd()) {
            $sizes['md'] = $this->getSizeMd();
        }
        if ($this->getSizeLg()) {
            $sizes['lg'] = $this->getSizeLg();
        }

        if (!\count($sizes)) {
            if ($this->getSize()) {
                $sizes['sm'] = $this->getSize();
            }
        }

        return $sizes;
    }
}
