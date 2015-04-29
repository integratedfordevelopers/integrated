<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Channel\Connector\Config;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Config implements ConfigInterface
{
    private $name;

    /**
     * @var string
     */
    private $adaptor;

    /**
     * @var OptionsInterface
     */
    private $options;

    /**
     * Constructor.
     *
     * @param string           $name
     * @param string           $adaptor
     * @param OptionsInterface $options
     */
    public function __construct($name, $adaptor, OptionsInterface $options)
    {
        $this->name = $name;
        $this->adaptor = $adaptor;
        $this->options = $options;
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
    public function getAdapter()
    {
        return $this->adaptor;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->options;
    }
}
