<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SocialBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class IntegratedSocialExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('form.xml');

        $config = $this->processConfiguration(new Configuration(), $configs);

        foreach (\array_keys($config) as $connector) {
            $loader->load($connector.'.xml');
        }

        if (isset($config['twitter'])) {
            $defintion = $container->getDefinition('integrated_social.twitter.factory');

            $defintion->replaceArgument(0, $config['twitter']['consumer_key']);
            $defintion->replaceArgument(1, $config['twitter']['consumer_secret']);
        }

        if (isset($config['facebook'])) {
            $defintion = $container->getDefinition('integrated_social.facebook');
            $defintion->replaceArgument(0, [
                'app_id' => $config['facebook']['app_id'],
                'app_secret' => $config['facebook']['app_secret'],
            ]);
        }
    }
}
