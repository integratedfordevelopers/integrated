<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Extension for loading configuration.
 *
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class IntegratedUserExtension extends Extension
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

        $loader->load('form.xml');

        $loader->load('manager.xml');
        $loader->load('manager.doctrine.xml');

        $loader->load('security.xml');
        $loader->load('extension.xml');

        $loader->load('event_listeners.xml');

        $loader->load('command.xml');

        $loader->load('repository.xml');

        $loader->load('data_fixtures.xml');

        $loader->load('controller.xml');

        $loader->load('services.xml');

        // @TODO make it a config option to enable the default mapping or not
        $container->setParameter('integrated_user.mapping.enabled', true);
    }
}
