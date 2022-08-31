<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr\Indexer;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Job implements JobInterface
{
    /**
     * @var string
     */
    private $action;

    /**
     * @var string[]
     */
    private $options = [];

    /**
     * The constructor.
     *
     * @param string|null $action  the action or null of not specified
     * @param string[]    $options array of options
     */
    public function __construct($action = null, array $options = [])
    {
        $this->setAction($action);

        foreach ($options as $name => $value) {
            $this->setOption($name, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(['action' => $this->action, 'options' => $this->options]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        $this->__construct($data['action'], (array) $data['options']);
    }

    /**
     * Set the action.
     *
     * If $action is null then the action will be unset.
     *
     * @param string|null $action
     *
     * @return $this
     */
    public function setAction($action)
    {
        $this->action = $action === null ? $action : (string) $action;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * {@inheritdoc}
     */
    public function hasAction()
    {
        return (bool) $this->action;
    }

    /**
     * Set the option.
     *
     * @param string $name  the option name
     * @param string $value
     *
     * @return $this
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = (string) $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOption($name)
    {
        return isset($this->options[$name]) ? $this->options[$name] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function hasOption($name)
    {
        return isset($this->options[$name]);
    }

    /**
     * Remove the option.
     *
     * @param string $name the name of the option to remove
     *
     * @return $this
     */
    public function removeOption($name)
    {
        unset($this->options[$name]);

        return $this;
    }

    /**
     * Get all the options.
     *
     * @return string[]
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Remove all the options.
     *
     * @return $this
     */
    public function clearOptions()
    {
        $this->options = [];

        return $this;
    }

    public function __serialize(): array
    {
        return ['action' => $this->action, 'options' => $this->options];
    }

    public function __unserialize(array $data)
    {
        $this->__construct($data['action'], (array) $data['options']);
    }
}
