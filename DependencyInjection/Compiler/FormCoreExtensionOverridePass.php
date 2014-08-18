<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\FormTypeBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class FormCoreExtensionOverridePass implements CompilerPassInterface
{
	/**
	 * @inheritdoc
	 */
	public function process(ContainerBuilder $container)
	{
		if (!$container->hasDefinition('form.type.checkbox')) {
			return;
		}

		// overwrite the original checkbox with a new class that will
		// set align_with_widget to true by default for check and also
		// radio box.

		$builder = $container->getDefinition('form.type.checkbox');
		$builder->setClass('%integrated_formtype.form.checkbox.type.class%');
	}

}