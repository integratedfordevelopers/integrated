<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class IntegratedStorageExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @var string
     */
    protected $formTemplate = '@IntegratedStorage/form/form_div_layout.html.twig';

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('command.xml');
        $loader->load('controllers.xml');
        $loader->load('database.xml');
        $loader->load('event_listeners.xml');
        $loader->load('form.xml');
        $loader->load('services.xml');
        $loader->load('solr.xml');
        $loader->load('data_fixtures.xml');

        // Inject the "resolve" config (app/config.yml) in the file resolver service
        $container->getDefinition('integrated_storage.resolver')
            ->replaceArgument(0, $config['resolver'])
            // Validation of the class will happen when the dependency has been created
            ->replaceArgument(1, new Definition($config['identifier_class']));

        // Inject the "decision" config (app/config.yml) in the manager
        $container->getDefinition('integrated_storage.decision')
            ->replaceArgument(1, $config['decision_map']);
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
                        ['form_themes' => [$this->formTemplate]]
                    );
                    break;
            }
        }
    }
}
