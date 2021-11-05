<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ChannelBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder('integrated_channel');
        $builder->getRootNode()
            ->children()
                ->arrayNode('configs')
                    ->prototype('array')
                        ->canBeDisabled()
                        ->children()
                            ->scalarNode('adaptor')->cannotBeEmpty()->end()
                            ->arrayNode('options')
                                ->defaultValue([])
                                ->prototype('variable')->end()
                            ->end()
                            ->arrayNode('channel')
                                ->beforeNormalization()
                                    ->ifString()
                                    ->then(function ($v) {
                                        return [$v];
                                    })
                                ->end()
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $builder;
    }
}
