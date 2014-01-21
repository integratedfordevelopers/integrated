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

use ReflectionClass;

use Symfony\Component\Config\Resource\FileResource;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class RegisterConverterFileReaderMappingPass implements CompilerPassInterface
{
	/**
	 * @inheritdoc
	 */
	public function process(ContainerBuilder $container)
	{
		if (!$container->hasDefinition('integrated_solr.converter.resolver.chain')) {
			return;
		}

		$definition = $container->getDefinition('integrated_solr.converter.resolver.chain');

		foreach ($container->getParameter('kernel.bundles') as $name => $class) {
			if (null !== ($ref = $this->addResolver($container, dirname((new ReflectionClass($class))->getFileName()), $name))) {
				$definition->addMethodCall('addResolver', array($ref));
			}
		}
	}

	protected function addResolver(ContainerBuilder $container, $dir, $bundle)
	{
		if ($container->hasDefinition('integrated_solr.converter.resolver.file.' . $bundle)) {
			return null;
		}

		// check if the bundle has a config/solr directory and then add
		// it to the resolver chain

		if (!is_dir($dir . '/Resources/config/solr')) {
			return null;
		}

		$args = array(
			new Reference('integrated_solr.converter.resolver.file.reader.yaml'),
			$this->addFinder($container, $dir . '/Resources/config/solr', $bundle)
		);

		$definition = new Definition('%integrated_solr.converter.resolver.file.class%', $args);
		$definition->setPublic(false);

		$container->setDefinition('integrated_solr.converter.resolver.file.' . $bundle, $definition);

		return new Reference('integrated_solr.converter.resolver.file.' . $bundle);
	}

	protected function addFinder(ContainerBuilder $container, $dir, $bundle)
	{
		$ref = new Reference('integrated_solr.converter.resolver.file.' . $bundle . '.finder');

		if ($container->hasDefinition('integrated_solr.converter.resolver.file.' . $bundle . '.finder')) {
			return $ref;
		}

		$definition = new Definition('%integrated_solr.converter.resolver.file.finder.class%');
		$definition->setPublic(false);

		$definition->addMethodCall('files');
		$definition->addMethodCall('in', array($dir));
		$definition->addMethodCall('name', array('*.yml'));

		$container->setDefinition('integrated_solr.converter.resolver.file.' . $bundle . '.finder', $definition);

		$container->addResource(new FileResource($dir)); // not really sure what this does

		return $ref;
	}
}