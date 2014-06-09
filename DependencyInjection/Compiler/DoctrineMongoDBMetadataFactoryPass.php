<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class DoctrineMongoDBMetadataFactoryPass implements CompilerPassInterface
{
	/**
	 * @inheritdoc
	 */
	public function process(ContainerBuilder $container)
	{
		if (!$container->hasDefinition('doctrine_mongodb.odm.default_configuration')) {
			return;
		}

		$config = $container->getDefinition('doctrine_mongodb.odm.default_configuration');
		$config->addMethodCall('setClassMetadataFactoryName', array('Integrated\MongoDB\ContentType\Mapping\ClassMetadataFactory'));

		if (!$container->hasDefinition('doctrine_mongodb.odm.default_document_manager')) {
			return;
		}

		$manager = $container->getDefinition('doctrine_mongodb.odm.default_document_manager');

		$container->setDefinition(
			'integrated_content.odm.default_manager_configurator',
			new Definition('%integrated_content.odm.mongo.manager_configurator.class%', array($manager->getConfigurator()))
		);

		$manager->setConfigurator(array(new Reference('integrated_content.odm.default_manager_configurator'), 'configure'));
	}
}