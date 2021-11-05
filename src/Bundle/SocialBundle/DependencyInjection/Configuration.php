<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SocialBundle\DependencyInjection;

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
        $builder = new TreeBuilder('integrated_social');
        $builder->getRootNode()
            ->children()
                ->arrayNode('twitter')
                    ->children()
                        ->scalarNode('consumer_key')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('consumer_secret')->isRequired()->cannotBeEmpty()->end()
                    ->end()
                ->end()
                ->arrayNode('facebook')
                    ->children()
                        ->scalarNode('app_id')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('app_secret')->isRequired()->cannotBeEmpty()->end()
                    ->end()
                ->end()
            ->end();

        return $builder;
    }
}
