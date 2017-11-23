<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ExtensionRegistryBuilderPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('integrated_content.extension.registry.builder')) {
            return;
        }

        $builder = $container->getDefinition('integrated_content.extension.registry.builder');

        foreach ($container->findTaggedServiceIds('integrated_content.extension') as $service => $tags) {
            $builder->addMethodCall('addExtension', [$container->getDefinition($service)]);
        }
    }
}
