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

use Symfony\Component\Config\FileLocator;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;

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
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('converter.xml');
        $loader->load('event.xml');
        $loader->load('indexer.xml');
        $loader->load('queue.xml');
        $loader->load('solarium.xml');
        $loader->load('task.xml');
        $loader->load('types.xml');
        $loader->load('worker.xml');
        $loader->load('solr.xml');

        if ($container->getParameter('kernel.debug')) {
            $loader->load('collector.xml');
        }

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $endpoints = array();

        foreach ($config['endpoints'] as $name => $options) {
            $options['key'] = $name;
            $container->setDefinition(
                'solarium.client.endpoint.' . $name,
                new Definition('%integrated_solr.solarium.endpoint.class%', array($options))
            );

            $endpoints[] = new Reference('solarium.client.endpoint.' . $name);
        }

        $container->setDefinition(
            'solarium.client',
            new Definition('%integrated_solr.solarium.client.class%', array(array('endpoint' => $endpoints)))
        );

        if ($container->getParameter('kernel.debug')) {
            $container->getDefinition('solarium.client')->addMethodCall(
                'registerPlugin',
                array('solarium.client.logger', new Reference('integrated_solr.solarium.data_collector'))
            );
        }
    }
}
