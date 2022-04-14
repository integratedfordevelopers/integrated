<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\DependencyInjection\Security;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AuthenticatorFactoryInterface;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\FirewallListenerFactoryInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class IpListFactory implements AuthenticatorFactoryInterface, FirewallListenerFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createAuthenticator(ContainerBuilder $container, string $firewallName, array $config, string $userProviderId)
    {
        return [];
    }

    public function createListeners(ContainerBuilder $container, string $firewallName, array $config): array
    {
        $listenerId = 'integrated_user.security.authentication.listener.ip_list.'.$firewallName;

        $container->setAlias($listenerId, 'integrated_user.security.authentication.listener.ip_list');

        return [$listenerId];
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority(): int
    {
        return -30;
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return 'ip_list';
    }

    /**
     * {@inheritdoc}
     */
    public function addConfiguration(NodeDefinition $node)
    {
    }
}
