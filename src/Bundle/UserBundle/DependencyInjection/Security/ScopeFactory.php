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

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Integrated\Bundle\UserBundle\Security\Authentication\Provider\ScopeProvider;
use Integrated\Bundle\UserBundle\Security\Firewall\ScopeListener;

class ScopeFactory implements SecurityFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        $providerId = 'integrated_user.security.authentication.provider.scope.' . $id;
        $listenerId = 'integrated_user.security.authentication.listener.scope.' . $id;

        $container->setDefinition($providerId, new ChildDefinition(ScopeProvider::class));
        $container->setDefinition($listenerId, new ChildDefinition(ScopeListener::class))->replaceArgument(1, $id);

        return [$providerId, $listenerId, $defaultEntryPoint];
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
