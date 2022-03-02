<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\Security\TwoFactor;

class ConfigRegistry implements ConfigRegistryInterface
{
    /**
     * @var Config[]
     */
    private $configs;

    public function __construct(array $configs = [])
    {
        $this->configs = $configs;
    }

    public function hasConfig(string $firewall): bool
    {
        return isset($this->configs[$firewall]);
    }

    public function getConfig(string $firewall): Config
    {
        if (isset($this->configs[$firewall])) {
            return $this->configs[$firewall];
        }

        throw new \InvalidArgumentException(sprintf('There is no two-factor config for the firewall %s', $firewall));
    }
}
