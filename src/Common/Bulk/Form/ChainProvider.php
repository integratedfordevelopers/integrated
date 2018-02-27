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
class ChainProvider implements ConfigProviderInterface
{
    /**
     * @var ConfigProviderInterface[]
     */
    private $providers;

    /**
     * @param ConfigProviderInterface[] $providers
     */
    public function __construct(array $providers)
    {
        $this->providers = $providers;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig(array $content)
    {
        $config = [];

        foreach ($this->providers as $provider) {
            $config = array_merge($config, $provider->getConfig($content));
        }

        return $config;
    }
}
