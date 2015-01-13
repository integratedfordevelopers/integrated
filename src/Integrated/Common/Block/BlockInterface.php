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
     * Return the id of the block
     *
     * @return string
     */
    public function getId();

    /**
     * Return the short name of the block
     *
     * @return string
     */
    public function getShortName();

    /**
     * Return the controller of the block
     *
     * @return string
     */
    public function getController();
}
