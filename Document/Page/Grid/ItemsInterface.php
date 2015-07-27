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

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
interface ItemsInterface
{
    /**
     * @return array
     */
    public function getItems();

    /**
     * @param array $items
     * @return $this
     */
    public function setItems(array $items = []);

    /**
     * @param Item $item
     * @return $this
     */
    public function addItem(Item $item);

    /**
     * @param Item $item
     * @return $this
     */
    public function removeItem(Item $item);
}
