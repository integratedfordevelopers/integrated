<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Form\Mapping\Annotations;

use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Annotation for defining field options for properties of a document.
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 * @Annotation
 */
class Field
{
    /**
     * @var string
     */
    protected $type = TextType::class;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * Constructor.
     *
     * @param array $data
     *
     * @throws \BadMethodCallException
     */
    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {
            $method = 'set'.str_replace('_', '', $key);
            if (!method_exists($this, $method)) {
                throw new \BadMethodCallException(sprintf("Unknown property '%s' on annotation '%s'.", $key, \get_class($this)));
            }
            $this->$method($value);
        }
    }

    /**
     * Get the type of the field.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the type of the field.
     *
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the options of the field.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set the label of the field.
     *
     * @param array $options
     *
     * @return $this
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }
}
