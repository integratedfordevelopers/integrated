<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * IntegratedContentExtension for loading configuration
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class IntegratedContentExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @var string
     */
    protected $formTemplate = 'IntegratedContentBundle:Form:form_div_layout.html.twig';

    /**
     * Load the configuration
     *
     * @param array $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('controller.xml');

        $loader->load('channel.xml');
        $loader->load('converters.xml');
        $loader->load('extensions.xml');

        $loader->load('paginator.xml');

        $loader->load('form.xml');
        $loader->load('form.registry.xml');

        $loader->load('manager.xml');
        $loader->load('manager.doctrine.xml');

        $loader->load('metadata.xml');
        $loader->load('mongo.xml');
        $loader->load('resolvers.xml');
        $loader->load('solr.xml');
        $loader->load('twig.xml');
        $loader->load('event_listeners.xml');
        $loader->load('event_dispatchers.xml');
        $loader->load('repositories.xml');
        $loader->load('menu.xml');

        $loader->load('security.xml');

        $loader->load('provider.xml');
        $loader->load('block.xml');
        $loader->load('bulk.xml');

        $loader->load('serializer.xml');
        $loader->load('json-ld.xml');

        $loader->load('services.xml');
        $loader->load('mailer.xml');

        $configuration = new Configuration();
        $this->processConfiguration($configuration, $configs);
    }

    /**
     * {@inheritDoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $this->configureTwigBundle($container);
    }

    /**
     * @param ContainerBuilder $container The service container
     *
     * @return void
     */
    protected function configureTwigBundle(ContainerBuilder $container)
    {
        foreach ($container->getExtensions() as $name => $extension) {
            switch ($name) {
                case 'twig':
                    $container->prependExtensionConfig(
                        $name,
                        array('form'  => array('resources' => array($this->formTemplate)))
                    );
                    break;
            }
        }
    }
}
