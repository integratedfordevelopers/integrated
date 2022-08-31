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
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Integrated\Bundle\UserBundle\Security\Firewall\ScopeListener;

class ScopeFactory implements AuthenticatorFactoryInterface, FirewallListenerFactoryInterface
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
        $listenerId = 'integrated_user.security.authentication.listener.scope.'.$firewallName;

        $container->setDefinition($listenerId, new ChildDefinition(ScopeListener::class))->replaceArgument(1, $firewallName);

        return [$listenerId];
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return 'remember_me';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority(): int
    {
        return -40;
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return 'scope';
    }

    /**
     * {@inheritdoc}
     */
    public function addConfiguration(NodeDefinition $node)
    {
    }
}
