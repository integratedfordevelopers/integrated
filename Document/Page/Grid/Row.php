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
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Row document
 *
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 *
 * @ODM\EmbeddedDocument
 */
class Row
{
    /**
     * @var array
     * @ODM\EmbedMany(targetDocument="Integrated\Bundle\WebsiteBundle\Document\Page\Grid\Column")
     */
    protected $columns;

    /**
     */
    public function __construct()
    {
        $this->columns = new ArrayCollection();
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns->toArray();
    }

    /**
     * @param array $columns
     * @return $this
     */
    public function setColumns(array $columns)
    {
        $this->columns = new ArrayCollection($columns);
        return $this;
    }

    /**
     * @param Column $column
     * @return $this
     */
    public function addColumn(Column $column)
    {
        $this->columns->add($column);
        return $this;
    }

    /**
     * @param Column $column
     * @return $this
     */
    public function removeColumn(Column $column)
    {
        $this->columns->removeElement($column);
        return $this;
    }
}