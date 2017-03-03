<?php

namespace Integrated\Bundle\WorkflowBundle\Form\Model;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class State
{
    /**
     * @var string
     */
    protected $value;

    /**
     * @var string
     */
    protected $label;

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;
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
}
