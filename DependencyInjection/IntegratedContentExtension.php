<?php
namespace Integrated\Bundle\ContentBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\Config\FileLocator,
    Symfony\Component\HttpKernel\DependencyInjection\Extension,
    Symfony\Component\DependencyInjection\Loader;

/**
 * IntegratedContentExtension for loading configuration
 *
 * @package Integrated\Bundle\ContentBundle\DependencyInjection
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class IntegratedContentExtension extends Extension
{
    /**
     * Load the configuration
     *
     * @param array $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $configuration = new Configuration();
        $this->processConfiguration($configuration, $configs);
    }
}