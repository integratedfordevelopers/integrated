<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Bulk\Form;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Config implements ConfigInterface
{
    /**
     * @var string
     */
    private $handler;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $options;

    /**
     * @var ActionMatcherInterface
     */
    private $matcher;

    /**
     * @param string                 $handler
     * @param string                 $name
     * @param string                 $type
     * @param array                  $options
     * @param ActionMatcherInterface $matcher
     */
    public function __construct($handler, $name, $type, array $options, ActionMatcherInterface $matcher)
    {
        $this->handler = $handler;
        $this->name = $name;
        $this->type = $type;
        $this->options = $options;
        $this->matcher = $matcher;
    }

    /**
     * {@inheritdoc}
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
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
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function getMatcher()
    {
        return $this->matcher;
    }
}
