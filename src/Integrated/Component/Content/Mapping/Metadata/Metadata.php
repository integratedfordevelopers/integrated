<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active BV <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Component\Content\Mapping\Metadata;

/**
 * Class for storing metadata properties of a document
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class Metadata
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $fields = array();

    /**
     * Get the name of the Metadata
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the name of the Metadata
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }


    /**
     * @param array $fields
     * @return $this
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function addField($key, $value)
    {
        $this->fields[$key] = $value;
        return $this;
    }

    /**
     * @param string $key
     * @return $this
     */
    public function removeField($key)
    {
        unset($this->fields[$key]);
        return $this;
    }
}