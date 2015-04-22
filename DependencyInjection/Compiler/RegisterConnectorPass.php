<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ChannelBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class RegisterConnectorPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('integrated_channel.connector.adaptor.registry_builder')) {
            return;
        }

        $definition = $container->getDefinition('integrated_channel.connector.adaptor.registry_builder');

        foreach ($container->findTaggedServiceIds('integrated_channel.connector') as $service => $tags) {
            $definition->addMethodCall('addAdaptor', [new Reference($service)]);
        }
    }
}
