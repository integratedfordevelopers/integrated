<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SolrBundle\DependencyInjection\CompilerPass;

use Integrated\Common\Converter\Config\Provider\XmlProvider;
use ReflectionClass;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Finder\Finder;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class RegisterConfigFileProviderPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('integrated_solr.converter.config.provider.chain')) {
            return;
        }

        $definition = $container->getDefinition('integrated_solr.converter.config.provider.chain');

        foreach ($container->getParameter('kernel.bundles') as $name => $class) {
            if (null !== (
                $ref = $this->addProvider($container, \dirname((new ReflectionClass($class))->getFileName()), $name)
            )) {
                $definition->addMethodCall('addProvider', [$ref]);
            }
        }
    }

    /**
     * Create a provider definition.
     *
     * Create the definition of a file provider instance if there is a possibility that this given bundle
     * got any solr config files. But only if the container does not already contain a service definition
     * with the same name.
     *
     * @param ContainerBuilder $container
     * @param string           $dir
     * @param string           $bundle
     */
    protected function addProvider(ContainerBuilder $container, $dir, $bundle)
    {
        if ($container->hasDefinition('integrated_solr.converter.config.provider.file.'.$bundle)) {
            return null;
        }

        // If the bundle got a config/solr directory then a provider is created for this bundle. This
        // however still does not mean that the bundle actually got any solr config files but that is
        // not really a problem.

        if (!is_dir($dir.'/Resources/config/solr')) {
            return null;
        }

        $definition = new Definition(XmlProvider::class);
        $definition->setPublic(false);
        $definition->setArguments([$this->addFinder($container, $dir.'/Resources/config/solr', $bundle)]);

        $container->setDefinition('integrated_solr.converter.config.provider.file.'.$bundle, $definition);

        return new Reference('integrated_solr.converter.config.provider.file.'.$bundle);
    }

    /**
     * Create a finder definition.
     *
     * Create the definition of a finder instance that will look for files in the bundle. If the finder
     * definition already exists then that one is returned.
     *
     * @param ContainerBuilder $container
     * @param string           $dir
     * @param string           $bundle
     *
     * @return Reference
     */
    protected function addFinder(ContainerBuilder $container, $dir, $bundle)
    {
        $ref = new Reference('integrated_solr.converter.config.provider.file.'.$bundle.'.finder');

        if ($container->hasDefinition('integrated_solr.converter.config.provider.file.'.$bundle.'.finder')) {
            return $ref;
        }

        $container->addResource(new FileResource($dir)); // not really sure what this does

        $definition = new Definition(Finder::class);
        $definition->setPublic(false);
        $definition->addMethodCall('in', [$dir]);

        $container->setDefinition('integrated_solr.converter.config.provider.file.'.$bundle.'.finder', $definition);

        return $ref;
    }
}
