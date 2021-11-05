<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('integrated_storage');
        $rootNode = $treeBuilder->getRootNode();

        $this->addRootConfig($rootNode);
        $this->addResolverConfig($rootNode);
        $this->addFilesystemDecisionMap($rootNode);

        return $treeBuilder;
    }

    /**
     * Adds the default identifier_class to the root node.
     *
     * @param ArrayNodeDefinition $node
     */
    protected function addRootConfig(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->scalarNode('identifier_class')
                ->defaultValue('Integrated\Bundle\StorageBundle\Storage\Identifier\FileIdentifier')
                ->end();
    }

    /**
     * Adds the required resolvers key to the root node.
     *
     * @param ArrayNodeDefinition $node
     */
    protected function addResolverConfig(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('resolver')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                    ->children()
                        ->scalarNode('public')
                            ->isRequired()
                            ->end()
                        ->scalarNode('resolver_class')
                            ->defaultValue('Integrated\Bundle\StorageBundle\Storage\Resolver\LocalResolver')
                            ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * Adds the optional decision map to to the root node.
     *
     * @param ArrayNodeDefinition $node
     */
    protected function addFilesystemDecisionMap(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('decision_map')
                    ->useAttributeAsKey('class')
                    ->prototype('array')
                        ->useAttributeAsKey('name')
                        ->prototype('scalar')->end()
                ->end()
            ->end()
        ->end();
    }
}
