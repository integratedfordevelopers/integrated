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
class RegisterConfigResolverPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('integrated_channel.config.resolver.priority_builder')) {
            return;
        }

        $definition = $container->getDefinition('integrated_channel.config.resolver.priority_builder');

        foreach ($container->findTaggedServiceIds('integrated_channel.config.resolver') as $service => $tags) {
            $arguments = [new Reference($service)];

            if (isset($tags[0]['priority'])) {
                $arguments[] = (int) $tags[0]['priority'];
            }

            $definition->addMethodCall('addResolver', $arguments);
        }
    }
}
