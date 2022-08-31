<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WorkflowBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Extension for loading configuration.
 *
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class IntegratedWorkflowExtension extends Extension implements PrependExtensionInterface
{
    /**
     * Load the configuration.
     *
     * @param array            $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('controller.xml');
        $loader->load('command.xml');

        $loader->load('doctrine.xml');
        $loader->load('extension.xml');
        $loader->load('repository.xml');

        $loader->load('form.xml');

        $loader->load('security.xml');

        $loader->load('service.xml');

        $loader->load('lock.xml');
        $loader->load('queue.xml');
        $loader->load('solr.xml');

        $loader->load('event_listeners.xml');
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $this->configureTwigBundle($container);
    }

    /**
     * @param ContainerBuilder $container The service container
     */
    protected function configureTwigBundle(ContainerBuilder $container)
    {
        foreach ($container->getExtensions() as $name => $extension) {
            switch ($name) {
                case 'twig':
                    $container->prependExtensionConfig($name, ['form_themes' => ['@IntegratedWorkflow/form/form_div_layout.html.twig']]);
                    break;
            }
        }
    }
}
