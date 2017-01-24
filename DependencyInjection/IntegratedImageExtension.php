<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ImageBundle\DependencyInjection;

use Integrated\Bundle\ImageBundle\DependencyInjection\CompilerPass\ImageConverterCompilerPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class IntegratedImageExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        // Load the bundle service configuration
        $loader->load('converter.xml');
        $loader->load('services.xml');
        $loader->load('twig.xml');
        $loader->load('validator.xml');
    }
}
