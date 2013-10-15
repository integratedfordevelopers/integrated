<?php
namespace Integrated\Bundle\ContentBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder,
    Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration class for ContentBundle
 *
 * @package Integrated\Bundle\ContentBundle\DependencyInjection
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('integrated_content');

        return $treeBuilder;
    }
}