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
class RegisterConfigPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('integrated_channel.connector.config.resolver.memory_builder')) {
            return;
        }

        $definition = $container->getDefinition('integrated_channel.connector.config.resolver.memory_builder');

        foreach ($container->findTaggedServiceIds('integrated_channel.config') as $service => $tags) {
            $channels = [];

            foreach ($tags as $attributes) {
                $channels[] = isset($attributes['channel']) ? (string) $attributes['channel'] : null;
            }

            $channels = array_unique($channels);

            foreach ($channels as $channel) {
                $definition->addMethodCall('addConfig', [new Reference($service), $channel]);
            }
        }
    }
}
