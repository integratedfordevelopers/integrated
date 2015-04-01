<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Common\Block;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
interface BlockInterface
{
    /**
     * Get the block id
     *
     * @return string
     */
    public function getId();

    /**
     * Get the block title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Get the block layout
     *
     * @return string
     */
    public function getLayout();

    /**
     * Get the block type
     *
     * @return string
     */
    public function getType();
}
