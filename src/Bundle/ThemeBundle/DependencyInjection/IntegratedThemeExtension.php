<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ThemeBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class IntegratedThemeExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('controller.xml');
        $loader->load('event_listener.xml');
        $loader->load('services.xml');

        $definition = $container->getDefinition('integrated_theme.templating.theme_manager');

        foreach ($config['themes'] as $id => $theme) {
            $definition->addMethodCall('registerTheme', [$id, $theme['paths'], $theme['fallback']]);
        }
    }
}
