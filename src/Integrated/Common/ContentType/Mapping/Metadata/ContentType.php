<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\ContentType\Mapping\Metadata;

/**
 * Class for storing metadata properties of a Document
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ContentType
{
    /**
     * @var string
     */
    protected $class;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var ContentTypeField[]
     */
    protected $fields = array();

    /**
     * Get the class of the Document
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set the class of the Document
     *
     * @param string $class
     * @return $this
     */
    public function setClass($class)
    {
        $this->class = $class;
        return $this;
    }

    /**
     * Get the type of the Document
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the type of the Document
     *
     * @param string type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
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
     * @return ContentTypeField|null
     */
    public function getField($name)
    {
        foreach ($this->fields as $field) {
            if ($field->getName() == $name) {
                return $field;
            }
        }

        return null;
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