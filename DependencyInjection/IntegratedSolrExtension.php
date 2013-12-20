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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * IntegratedContentExtension for loading configuration
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class IntegratedSolrExtension extends Extension
{
    /**
     * Load the configuration
     *
     * @param array $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $configuration = new Configuration;

        $config = $this->processConfiguration($configuration, $configs);

        if ((isset($config['mapping']['directories'])) && is_array($config['mapping']['directories'])) {
            $directories = array();
            foreach ($config['mapping']['directories'] as $directory) {
                $directories[$directory['namespace_prefix']] = $directory['path'];
            }

            $container
                ->getDefinition('integrated_solr.mapping.driver.file_locator')
                ->replaceArgument(0, $directories)
            ;
        }
    }
}