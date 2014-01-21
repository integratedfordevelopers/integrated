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
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

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
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

		$loader->load('converter.xml');
		$loader->load('indexer.xml');
		$loader->load('queue.xml');
		$loader->load('solarium.xml');

		$configuration = new Configuration();
		$this->processConfiguration($configuration, $configs);
    }
}