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
 * Class for storing Solr config for a Document/Entity
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class Metadata
{
    /**
     * @var string
     */
    protected $class;

    /**
     * @var bool
     */
    protected $index = false;

    /**
     * @var MetadataField[]
     */
    protected $fields = array();

    /**
     * @param string $class
     */
    public function __construct($class = null)
    {
        $this->class = $class;
    }

    /**
     * @param string $class
     * @return $this
     */
    public function setClass($class)
    {
        $this->class = $class;
        return $this;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
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
     * @return boolean
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @param MetadataField[] $fields
     * @return $this
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * @return MetadataField[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    public function addField(MetadataField $field)
    {
        $this->fields[] = $field;
    }
}