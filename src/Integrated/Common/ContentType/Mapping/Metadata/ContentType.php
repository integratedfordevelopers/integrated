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

use Integrated\Common\ContentType\Mapping\MetadataFieldInterface;
use Integrated\Common\ContentType\Mapping\MetadataEditorInterface;

use ReflectionClass;

/**
 * Class for storing metadata properties of a Document
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ContentType implements MetadataEditorInterface
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
     * @var MetadataFieldInterface[]
     */
    protected $fields = [];

	/**
	 * @param $class
	 */
	public function __construct($class)
	{
		$this->class = $class;
	}

	/**
	 * @return bool
	 */
	public function isContent()
	{
		$reflection = $this->getReflection();
		return $reflection->implementsInterface(self::CONTENT) && $reflection->isInstantiable();
	}

	/**
	 * @inheritdoc
	 */
	public function getReflection()
	{
		if ($this->reflection === null) {
			$this->reflection = new ReflectionClass($this->class);
		}

		return $this->reflection;
	}

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
     * @inheritdoc
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @inheritdoc
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getFields()
    {
        return $this->fields;
    }

	/**
  	 * @inheritdoc
  	 */
    public function getField($name)
    {
		return $this->hasField($name) ? $this->fields[$name] : null;
    }

	/**
	 * @inheritdoc
	 */
	public function hasField($name)
	{
		return isset($this->fields[$name]);
	}

	/**
	 * @inheritdoc
	 */
	public function newField($name)
	{
		return new ContentTypeField($name);
	}

    /**
     * @inheritdoc
     */
    public function addField(MetadataFieldInterface $field)
    {
        $this->fields[$field->getName()] = $field;
        return $this;
    }
}