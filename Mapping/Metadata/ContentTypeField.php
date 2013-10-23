<?php
namespace Integrated\Bundle\ContentBundle\Mapping\Metadata;
use Integrated\Component\Content\ContentTypeFieldInterface;

/**
 * Class for storing metadata properties of a field
 *
 * @package Integrated\Bundle\ContentBundle\Mapping\Metadata
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ContentTypeField
{
    protected $name;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var bool
     */

    protected $required;
    /**
     * Get the name of the Field
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the name of the Field
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
     * Get the type of the Field
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the type of the Field
     *
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }


    /**
     * @param string $label
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return bool
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * @param bool $required
     * @return $this
     */
    public function setRequired($required)
    {
        $this->required = $required;
        return $this;
    }
}