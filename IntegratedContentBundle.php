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

	public function boot()
	{
		// TODO: move this to a better location and make it configurable

		if ($this->container->has('doctrine_mongodb.odm.default_document_manager')) {
			/** @var \Doctrine\ODM\MongoDB\DocumentManager $manager */
			$manager = $this->container->get('doctrine_mongodb.odm.default_document_manager');
			$manager->getMetadataFactory()->addManagedClass('Integrated\Bundle\ContentBundle\Document\Content\Content');
		}
	}
}
