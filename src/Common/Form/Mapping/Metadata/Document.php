<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Form\Mapping\Metadata;

use Integrated\Common\Form\Mapping\AttributeInterface;
use Integrated\Common\Form\Mapping\MetadataEditorInterface;
use ReflectionClass;

/**
 * Class for storing metadata properties of a Document.
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class Document implements MetadataEditorInterface
{
    /**
     * @var ReflectionClass
     */
    private $reflection = null;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var AttributeInterface[]
     */
    protected $fields = [];

    /**
     * @var AttributeInterface[]
     */
    protected $options = [];

    /**
     * @param $class
     */
    public function __construct($class)
    {
        $this->class = $class;
    }

    /**
     * {@inheritdoc}
     */
    public function isTypeOf($class)
    {
        if (null === $class) {
            return true;
        }

        $reflection = $this->getReflection();

        return (
            (interface_exists($class) && $reflection->implementsInterface($class)) ||
            $reflection->isSubclassOf($class) ||
            (class_exists($class) && $reflection->isInstance(new $class()))
        )
            &&
            $reflection->isInstantiable();
    }

    /**
     * {@inheritdoc}
     */
    public function getReflection()
    {
        if ($this->reflection === null) {
            $this->reflection = new ReflectionClass($this->class);
        }

        return $this->reflection;
    }

    /**
     * Get the class of the Document.
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
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
        return $this->hasField($name) ? $this->fields[$name] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function hasField($name)
    {
        return isset($this->fields[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function newField($name)
    {
        return new Field($name);
    }

    /**
     * {@inheritdoc}
     */
    public function addField(AttributeInterface $field)
    {
        $this->fields[$field->getName()] = $field;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function getOption($name)
    {
        return $this->hasOption($name) ? $this->options[$name] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function hasOption($name)
    {
        return isset($this->options[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function newOption($name)
    {
        return new Field($name);
    }

    /**
     * {@inheritdoc}
     */
    public function addOption(AttributeInterface $option)
    {
        $this->options[$option->getName()] = $option;

        return $this;
    }
}
