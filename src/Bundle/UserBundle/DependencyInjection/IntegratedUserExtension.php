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

use Integrated\Bundle\UserBundle\Security\TwoFactor\Config;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpFoundation\RequestMatcher;
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
        $loader->load('two_factor.xml');
        $loader->load('twig.xml');

        $config = $this->processConfiguration(new Configuration(), $configs);

        $this->configureTwoFactor($container, $config['two_factor']);

        // @TODO make it a config option to enable the default mapping or not
        $container->setParameter('integrated_user.mapping.enabled', true);
    }

    private function configureTwoFactor(ContainerBuilder $container, array $config): void
    {
        $container->getDefinition('integrated_user.event_listener.two_factor_activation_subscriber')
            ->setArgument(1, new Reference($config['whitelist_provider']));

        $matchers = [];

        foreach ($config['whitelist'] as $path) {
            $matchers[] = new Definition(RequestMatcher::class, [$path]);
        }

        $container->getDefinition('integrated_user.two_factor.whitelist_matcher')->setArgument(0, $matchers);

        $configs = [];

        foreach ($config['firewall'] as $firewall => $options) {
            $configs[$firewall] = new Definition(Config::class, [
                $options['required'],
                $options['activate_path'],
                $options['activate_check_path'],
                $options['default_target_path'],
            ]);
        }

        $container->getDefinition('integrated_user.two_factor.config_registry')->setArgument(0, $configs);

        $alwaysUseDefault = [];

        foreach ($config['firewall'] as $firewall => $options) {
            $alwaysUseDefault[$firewall] = (bool) $options['always_use_default_target_path'];
        }

        $container->getDefinition('integrated_user.two_factor.target_provider')->setArgument(0, $alwaysUseDefault);

        $templates = [];

        foreach ($config['firewall'] as $firewall => $options) {
            $templates[$firewall] = $options['template'];
        }

        $container->getDefinition('integrated_user.two_factor.handler_factory')->setArgument(0, $templates);
    }
}
