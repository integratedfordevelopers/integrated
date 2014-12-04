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

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class TemplatingPass implements CompilerPassInterface
{
	/**
	 * @inheritdoc
	 */
	public function process(ContainerBuilder $container)
	{
		if (!$container->hasParameter('templating.globals.class')) {
			return;
		}

		$container->setParameter('templating.globals.class', 'Integrated\\Bundle\\ContentBundle\\Templating\\GlobalVariables');
	}
}