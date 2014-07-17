<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WorkflowBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Extension for loading configuration
 *
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class IntegratedWorkflowExtension extends Extension
{
	/**
	 * Load the configuration
	 *
	 * @param array $configs
	 * @param ContainerBuilder $container
	 */
	public function load(array $configs, ContainerBuilder $container)
	{
		$loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

		$loader->load('extension.xml');

		$loader->load('form.xml');
		$loader->load('form.definition.xml');
		$loader->load('form.workflow.xml');

		$configuration = new Configuration();
		$config = $this->processConfiguration($configuration, $configs);
	}
}