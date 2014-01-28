<?php

namespace Integrated\Bundle\ContentBundle;

use Integrated\Bundle\ContentBundle\DependencyInjection\Compiler\DoctrineMongoDBMetadataFactoryPass;
use Integrated\Bundle\ContentBundle\DependencyInjection\IntegratedContentExtension;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class IntegratedContentBundle extends Bundle
{
	/**
	 * @inheritdoc
	 */
	public function build(ContainerBuilder $container)
	{
		$container->addCompilerPass(new DoctrineMongoDBMetadataFactoryPass());
	}

	/**
	 * @inheritdoc
	 */
	public function getContainerExtension()
	{
		return new IntegratedContentExtension();
	}
}
