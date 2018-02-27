<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Configurable
{
    /**
     * @var array
     */
    private $options;

    /**
     * @var OptionsResolver
     */
    private $resolver;

    /**
     * Configurable constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->resolver = new OptionsResolver();

        $this->configureOptions($this->resolver);
        $this->setOptions($options);
    }

    /**
     * Replace the options with a new set op options.
     *
     * @param array $options
     */
    public function setOptions(array $options = [])
    {
        $this->options = $this->resolver->resolve($options);
    }

    /**
     * Get all the options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set the value for the given key.
     *
     * @param $key
     * @param $value
     */
    public function setOption($key, $value)
    {
        $options = $this->getOptions();
        $options[$key] = $value;

        $this->setOptions($options);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasOption($key)
    {
        return isset($this->options[$key]);
    }

    /**
     * Get the option or return the default if none is set.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getOption($key)
    {
        return isset($this->options[$key]) ? $this->options[$key] : null;
    }

    /**
     * Configures the option resolver.
     *
     * @param OptionsResolver $resolver
     *
     * @codeCoverageIgnore
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
    }
}
