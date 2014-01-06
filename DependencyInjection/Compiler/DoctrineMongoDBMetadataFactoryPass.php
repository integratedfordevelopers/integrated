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
	}
}