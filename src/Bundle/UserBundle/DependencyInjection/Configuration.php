<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder('integrated_user');
        $builder->getRootNode()
            ->children()
                ->arrayNode('two_factor')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('firewall')
                            ->useAttributeAsKey('firewall')
                            ->arrayPrototype()
                                ->children()
                                    ->booleanNode('required')->defaultTrue()->end()
                                    ->scalarNode('activate_path')->defaultValue('integrated_user_two_factor_authenticator_activate')->end()
                                    ->scalarNode('activate_check_path')->defaultValue('integrated_user_two_factor_authenticator_activate_check')->end()
                                    ->scalarNode('default_target_path')->defaultValue('/')->end()
                                    ->booleanNode('always_use_default_target_path')->defaultFalse()->end()
                                    ->scalarNode('template')->defaultValue('@IntegratedUser/two_factor/google/activate.html.twig')->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('whitelist')
                            ->defaultValue([])
                            ->scalarPrototype()->end()
                        ->end()
                        ->scalarNode('whitelist_provider')->defaultValue('integrated_user.two_factor.whitelist_provider')->end()
                    ->end()
                ->end()
            ->end();

        return $builder;
    }
}
