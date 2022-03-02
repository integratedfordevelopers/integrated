<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\Security\TwoFactor\Http;

use Integrated\Bundle\UserBundle\Security\TwoFactor\ConfigRegistryInterface;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\FirewallMapInterface;

class ContextResolver implements ContextResolverInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $storage;

    /**
     * @var FirewallMapInterface
     */
    private $map;

    /**
     * @var ConfigRegistryInterface
     */
    private $registry;

    /**
     * @var ContextFactoryInterface
     */
    private $factory;

    public function __construct(TokenStorageInterface $storage, FirewallMapInterface $map, ConfigRegistryInterface $registry, ContextFactoryInterface $factory)
    {
        $this->storage = $storage;
        $this->map = $map;
        $this->registry = $registry;
        $this->factory = $factory;
    }

    public function resolve(Request $request): ?Context
    {
        if (!$this->map instanceof FirewallMap) {
            return null;
        }

        $token = $this->storage->getToken();

        if (!$token) {
            return null;
        }

        $config = $this->map->getFirewallConfig($request);

        if (!$config) {
            return null;
        }

        if ($this->registry->hasConfig($config->getName())) {
            return $this->factory->create($request, $token, $config->getName(), $this->registry->getConfig($config->getName()));
        }

        return null;
    }
}
