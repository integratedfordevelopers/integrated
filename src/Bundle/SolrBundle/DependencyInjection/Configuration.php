<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SolrBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration class for SolrBundle.
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();
        $builder->root('integrated_solr')
            ->children()
            ->arrayNode('endpoints')
            ->prototype('array')
            ->children()
            ->scalarNode('scheme')->defaultValue('http')->end()
            ->scalarNode('host')->defaultValue('localhost')->end()
            ->scalarNode('port')->defaultValue(8983)->end()
            ->scalarNode('username')->defaultValue(null)->end()
            ->scalarNode('password')->defaultValue(null)->end()
            ->scalarNode('path')->defaultValue('')->end()
            ->scalarNode('core')->end()
            ->scalarNode('timeout')->defaultValue(5)->end()
            ->end()
            ->end()
            ->end()
            ->end();

        return $builder;
    }
}
