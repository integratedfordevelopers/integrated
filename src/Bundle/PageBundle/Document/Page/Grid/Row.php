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

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Row document.
 *
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class Row
{
    /**
     * @var Column[]
     */
    protected $columns;

    public function __construct()
    {
        $this->columns = new ArrayCollection();
    }

    /**
     * @return Column[]
     */
    public function getColumns()
    {
        return $this->columns->toArray();
    }

    /**
     * @param array $columns
     *
     * @return $this
     */
    public function setColumns(array $columns)
    {
        $this->columns = new ArrayCollection($columns);

        return $this;
    }

    /**
     * @param Column $column
     *
     * @return $this
     */
    public function addColumn(Column $column)
    {
        $this->columns->add($column);

        return $this;
    }

    /**
     * @param Column $column
     *
     * @return $this
     */
    public function removeColumn(Column $column)
    {
        $this->columns->removeElement($column);

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $columns = [];

        foreach ($this->columns as $column) {
            $columns[] = $column->toArray();
        }

        $array = [];

        if (\count($columns)) {
            $array['columns'] = $columns;
        }

        return $array;
    }
}
