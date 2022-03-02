<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WorkflowBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Integrated\Bundle\WorkflowBundle\Entity\Definition\State;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Definition
{
    /**
     * @var string
     */
    protected $id = null;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var Collection|State[]
     */
    protected $states;

    /**
     * @var State
     */
    protected $default;

    public function __construct()
    {
        $this->states = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = (string) $name;

        return $this;
    }

    /**
     * @return State[]
     */
    public function getStates()
    {
        return $this->states->toArray();
    }

    /**
     * @param State[] $states
     *
     * @return $this
     */
    public function setStates(Collection $states)
    {
        foreach ($this->states as $state) {
            $this->removeState($state);
        }

        $this->states = new ArrayCollection();

        foreach ($states as $state) {
            $this->addState($state); // type check
        }

        return $this;
    }

    /**
     * @param State $state
     *
     * @return $this
     */
    public function addState(State $state)
    {
        if (!$this->states->contains($state)) {
            $this->states->add($state);

            // first add the state to the workflow then set the workflow else
            // there would be a infinite loop

            $state->setWorkflow($this);
        }

        return $this;
    }

    /**
     * @param State $state
     *
     * @return bool
     */
    public function hasState(State $state)
    {
        return $this->states->contains($state);
    }

    /**
     * @param State $state
     *
     * @return $this
     */
    public function removeState(State $state)
    {
        if ($this->states->removeElement($state)) {
            $state->setWorkflow(null);
        }

        return $this;
    }

    /**
     * @return State
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param State $default
     *
     * @return $this
     */
    public function setDefault(State $default = null)
    {
        $this->default = $default;

        return $this;
    }
}
