<?php

namespace Integrated\Bundle\SolrBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ExpanderPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('integrated_solr.solr_query.expander')) {
            return;
        }

        $definition = $container->getDefinition('integrated_solr.solr_query.expander');

        foreach ($container->findTaggedServiceIds('integrated_solr.expansion') as $service => $tags) {
            $definition->addMethodCall('addExpansion', [new Reference($service)]);
        }
    }
}
