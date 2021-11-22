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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class IntegratedChannelExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('adapters.xml');
        $loader->load('config.xml');
        $loader->load('exporter.xml');

        $loader->load('commands.xml');
        $loader->load('controllers.xml');

        $loader->load('doctrine.xml');

        $loader->load('form.xml');

        $loader->load('event_listeners.xml');

        $loader->load('queue.xml');

        $loader->load('manager.xml');
        $loader->load('repository.xml');

        $config = $this->processConfiguration(new Configuration(), $config);

        if (isset($config['configs'])) {
            $this->loadConfigs($config, $container);
        }
    }

    /**
     * Process the adaptor config configuration.
     *
     * @param array            $config
     * @param ContainerBuilder $container
     */
    protected function loadConfigs(array $config, ContainerBuilder $container)
    {
        foreach ($config['configs'] as $name => $arguments) {
            if (!$arguments['enabled']) {
                continue;
            }

            $id = 'integrated_channel.config.memory.'.$name;

            if ($container->hasDefinition($id)) {
                continue;
            }

            // first create the options and for that we need a unique service id

            do {
                $id_options = $id.'.options.'.uniqid();
            } while ($container->hasDefinition($id_options));

            $definition = new Definition('%integrated_channel.config.options.class%');
            $definition->setPublic(false);
            $definition->setArguments([$arguments['options']]);

            $container->setDefinition($id_options, $definition);

            // create the config it self

            $definition = new Definition('%integrated_channel.config.class%');
            $definition->setArguments([
                $name,
                $arguments['adaptor'],
                new Reference($id_options),
            ]);

            if ($arguments['channel']) {
                foreach ($arguments['channel'] as $channel) {
                    $definition->addTag('integrated_channel.config', ['channel' => $channel]);
                }
            } else {
                $definition->addTag('integrated_channel.config');
            }

            $container->setDefinition($id, $definition);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        foreach ($container->getExtensions() as $name => $extension) {
            switch ($name) {
                case 'twig':
                    $container->prependExtensionConfig(
                        $name,
                        ['form_themes' => ['@IntegratedChannel/form/options.html.twig']]
                    );
                    break;
            }
        }
    }
}
