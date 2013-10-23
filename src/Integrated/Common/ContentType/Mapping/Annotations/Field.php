<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\ContentType\Mapping\Annotations;

/**
 * Annotation for defining field options for properties of a document
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 * @Annotation
 */
class Field
{
    /**
     * @var string
     */
    protected $type = 'text';

    /**
     * @var string
     */
    protected $label;

    /**
     * @var bool
     */
    protected $required = false;

    /**
     * Constructor
     *
     * @param array $data
     * @throws \BadMethodCallException
     */
    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {
            $method = 'set'.str_replace('_', '', $key);
            if (!method_exists($this, $method)) {
                throw new \BadMethodCallException(sprintf("Unknown property '%s' on annotation '%s'.", $key, get_class($this)));
            }
            $this->$method($value);
        }
    }

    /**
     * Get the type of the field
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the type of the field
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
     * Get the label of the field
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set the label of the field
     *
     * @param string $label
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * Get the required of the field
     *
     * @return bool
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * Set the required of the field
     *
     * @param bool $required
     * @return $this
     */
    public function setRequired($required)
    {
        $this->required = (bool) $required;
        return $this;
    }
}