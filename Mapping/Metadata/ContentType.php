<?php
namespace Integrated\Bundle\ContentBundle\Mapping\Metadata;

/**
 * Class for storing metadata properties of a document
 *
 * @package Integrated\Bundle\ContentBundle\Mapping\Metadata
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ContentType
{
    /**
     * @var string
     */
    protected $className;

    /**
     * @var string
     */
    protected $classType;

    /**
     * @var ContentTypeField[]
     */
    protected $fields = array();

    /**
     * Get the className of the Document
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * Set the className of the Document
     *
     * @param string $className
     * @return $this
     */
    public function setClassName($className)
    {
        $this->className = $className;
        return $this;
    }

    /**
     * Get the classType of the Document
     *
     * @return string
     */
    public function getClassType()
    {
        return $this->classType;
    }

    /**
     * @param string $classType
     * @return $this
     */
    public function setClassType($classType)
    {
        $this->classType = $classType;
        return $this;
    }

    /**
     * @return ContentTypeField[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param $name
     * @return ContentTypeField|void
     */
    public function getField($name)
    {
        foreach ($this->fields as $field) {
            if ($field->getName() == $name) {
                return $field;
            }
        }
    }

    /**
     * @param array ContentTypeField[]
     * @return $this
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * @param ContentTypeField $field
     * @return $this
     */
    public function addField(ContentTypeField $field)
    {
        $this->fields[] = $field;
        return $this;
    }


}