<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Bundle\ContentBundle\Document\ContentType;

use Symfony\Component\Validator\Constraints as Assert;

use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Bundle\SlugBundle\Mapping\Annotations\Slug;

/**
 * Document ContentType
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ContentType implements ContentTypeInterface
{
    /**
     * @var string
     * @Slug(fields={"name"}, separator="_")
     */
    protected $id;

    /**
     * @var string The class of the content type
     * @Assert\NotBlank()
     */
    protected $class;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @var Embedded\Field[]
     */
    protected $fields = [];

    /**
     * @var mixed[]
     */
    protected $options = [];

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        $instance = new $this->class();
        $instance->setContentType($this->id);

        return $instance;
    }

    /**
     * Get the id of the content type
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the id of the content type
     *
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set the class of the content type
     *
     * @param string $class The class of the content type
     * @return $this
     */
    public function setClass($class)
    {
        $this->class = $class;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the name of content type
     *
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @deprecated use getId() instead
     */
    public function getType()
    {
        return $this->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * {@inheritdoc}
     */
    public function getField($name)
    {
        foreach ($this->getFields() as $field) {
            if ($field->getName() == $name) {
                return $field;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function hasField($name)
    {
        foreach ($this->getFields() as $field) {
            if ($field->getName() == $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * Set the fields of the content type
     *
     * @param array $fields
     * @return $this
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Overrider all the option with a new set of values for this content type
     *
     * @param string[] $options
     * @return $this
     */
    public function setOptions(array $options)
    {
        $this->options = [];

        foreach ($options as $name => $value) {
            $this->setOption($name, $value);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getOption($name)
    {
        if (isset($this->options[$name])) {
            return $this->options[$name];
        }

        return null;
    }

    /**
     * Set the value of the specified key.
     *
     * @param string $name
     * @param null | mixed $value
     * @return $this
     */
    public function setOption($name, $value = null)
    {
        if ($value === null) {
            unset($this->options[$name]);
        } else {
            $this->options[$name] = $value;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function hasOption($name)
    {
        return isset($this->options[$name]);
    }

    /**
     * Get the createdAt of the content type
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set the createdAt of the content type
     *
     * @param \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}
