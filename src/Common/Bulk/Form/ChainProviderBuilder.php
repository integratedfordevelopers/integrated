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
class ChainProviderBuilder
{
    /**
     * @var ConfigProviderInterface[]
     */
    private $providers = [];

    /**
     * @param ConfigProviderInterface $provider
     */
    public function addProvider(ConfigProviderInterface $provider)
    {
        $this->providers[spl_object_hash($provider)] = $provider;
    }

    /**
     * @return ChainProvider
     */
    public function getProvider()
    {
        return new ChainProvider(array_values($this->providers));
    }
}
