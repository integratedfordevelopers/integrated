<?php
namespace Integrated\Bundle\ContentBundle\Mapping\Metadata;

/**
 * Class for storing metadata properties of a document
 *
 * @package Integrated\Bundle\ContentBundle\Mapping\Metadata
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