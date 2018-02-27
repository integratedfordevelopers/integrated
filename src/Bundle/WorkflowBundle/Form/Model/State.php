<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
