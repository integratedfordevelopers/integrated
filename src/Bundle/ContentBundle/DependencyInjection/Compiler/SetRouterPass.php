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
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class SetRouterPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasAlias('router')) {
            return;
        }

        $id = $container->getAlias('router');

        $definition = $container->getDefinition('integrated_content.routing.router');
        $definition->setArgument(0, new Reference((string) $id));

        $container->setAlias('router', 'integrated_content.routing.router');
        $container->getAlias('router')->setPublic(true);
    }
}
