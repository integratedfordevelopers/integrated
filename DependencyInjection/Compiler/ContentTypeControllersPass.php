<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\PageBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class ContentTypeControllersPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('integrated_page.services.content_type_controller_manager')) {
            return;
        }

        $definition = $container->findDefinition('integrated_page.services.content_type_controller_manager');

        $taggedServices = $container->findTaggedServiceIds('integrated_page.contenttype_controller');

        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $definition->addMethodCall(
                    'addController',
                    [$id, $attributes]
                );
            }
        }
    }
}
