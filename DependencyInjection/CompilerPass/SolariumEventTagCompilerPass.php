<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SolrBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class SolariumEventTagCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('solarium.client');
        foreach ($container->findTaggedServiceIds('integrated_solr.event_subscriber') as $service => $tags) {
            $definition ->addMethodCall('getEventDispatcher()->addSubscriber', [new Reference($service)]);
        }
    }
}
