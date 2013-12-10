<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Bundle\SolrBundle\Mapping\Metadata;

/**
 * Class for storing Solr config for a Document/Entity property
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class MetadataField
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $index = false;

    /**
     * @var bool
     */
    protected $facet = false;

    /**
     * @var bool
     */
    protected $sort = false;

    /**
     * @var bool
     */
    protected $display = false;

    /**
     * @param string $name
     * @param bool $index
     * @param bool $facet
     * @param bool $sort
     * @param bool $display
     */
    public function __construct($name = null, $index = false, $facet = false, $sort = false, $display = false)
    {
        $this->name = $name;
        $this->index = $index;
        $this->facet = $facet;
        $this->sort = $sort;
        $this->display = $display;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param bool $index
     * @return $this
     */
    public function setIndex($index)
    {
        $this->index = $index;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @param bool $sort
     * @return $this
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
        return $this;
    }

    /**
     * @return bool
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @param bool $facet
     * @return $this
     */
    public function setFacet($facet)
    {
        $this->facet = $facet;
        return $this;
    }

    /**
     * @return bool
     */
    public function getFacet()
    {
        return $this->facet;
    }

    /**
     * @param bool $display
     * @return $this
     */
    public function setDisplay($display)
    {
        $this->display = $display;
        return $this;
    }

    /**
     * @return bool
     */
    public function getDisplay()
    {
        return $this->display;
    }
}