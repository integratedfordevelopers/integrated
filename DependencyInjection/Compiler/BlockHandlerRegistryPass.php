<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\BlockBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class BlockHandlerRegistryPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('integrated_block.registry.block_handler')) {
            return;
        }

        $definition = $container->getDefinition('integrated_block.registry.block_handler');

        foreach ($container->findTaggedServiceIds('integrated.block') as $id => $attributes) {
            if (isset($attributes[0]['type'])) {
                $definition->addMethodCall('registerHandler', [$attributes[0]['type'], new Reference($id)]);
            }
        }
    }
}
