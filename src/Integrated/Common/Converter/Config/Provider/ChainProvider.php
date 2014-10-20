<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Converter\Config\Provider;

use Integrated\Common\Converter\Config\TypeProviderInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ChainProvider implements TypeProviderInterface
{
    /**
     * @var TypeProviderInterface[]
     */
    private $providers = [];

    /**
     * {@inheritdoc}
     */
    public function getTypes($class)
    {
        $types = [];

        foreach ($this->providers as $provider) {
            $types = array_merge($types, $provider->getTypes($class));
        }

        return $types;
    }

    /**
     * Add the provider to the chain.
     *
     * @param TypeProviderInterface $provider
     */
    public function addProvider(TypeProviderInterface $provider)
    {
        if (!$this->hasProvider($provider)) {
            $this->providers[] = $provider;
        }
    }

    /**
     * Check if the provider is added to the chain.
     *
     * @param TypeProviderInterface $provider
     *
     * @return bool
     */
    public function hasProvider(TypeProviderInterface $provider)
    {
        if (false !== array_search($provider, $this->providers, true)) {
            return true;
        }

        return false;
    }

    /**
     * Remove the provider from the chain.
     *
     * @param TypeProviderInterface $provider
     */
    public function removeProvider(TypeProviderInterface $provider)
    {
        if (false !== ($key = array_search($provider, $this->providers, true))) {
            unset($this->providers[$key]);

            $this->providers = array_values($this->providers);
        }
    }

    /**
     * Get all the chained providers.
     *
     * @return TypeProviderInterface[]
     */
    public function getProviders()
    {
        return $this->providers;
    }

    /**
     * Clear all the providers from the chain.
     */
    public function clearProviders()
    {
        $this->providers = [];
    }
}
