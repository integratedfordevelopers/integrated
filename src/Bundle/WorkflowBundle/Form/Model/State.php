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
     * State constructor.
     *
     * @param string $value
     * @param string $label
     */
    public function __construct($value, $label)
    {
        $this->value = $value;
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }
}
