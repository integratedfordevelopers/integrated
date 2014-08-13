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

use Integrated\Common\ContentType\Mapping\AttributeEditorInterface;

/**
 * Class for storing metadata properties of a field
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ContentTypeAttribute implements AttributeEditorInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $options = [];

	/**
	 * @param string $name
	 */
	public function __construct($name)
	{
		$this->name = $name;
	}

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
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
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @inheritdoc
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
        return $this;
    }

	/**
	 * @inheritdoc
	 */
	public function getOption($name)
	{
		return $this->hasOption($name) ? $this->options[$name] : null;
	}

	/**
	 * @inheritdoc
	 */
	public function hasOption($name)
	{
		return isset($this->options[$name]);
	}

	/**
	 * @inheritdoc
	 */
	public function setOption($name, $value)
	{
		$this->options[$name] = $value;
		return $this;
	}

	/**
     * Shortcut to get the label of an element
     *
     * @return string
     */
    public function getLabel()
    {
        if (isset($this->options['label'])) {
            return $this->options['label'];
        }

        return ucfirst($this->name);
    }
}