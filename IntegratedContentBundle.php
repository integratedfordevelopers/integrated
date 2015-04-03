<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use Integrated\Bundle\ContentBundle\DependencyInjection\Compiler\ExtensionRegistryBuilderPass;
use Integrated\Bundle\ContentBundle\DependencyInjection\Compiler\FormFactoryEventDispatcherPass;
use Integrated\Bundle\ContentBundle\DependencyInjection\Compiler\MetadataEventDispatcherPass;
use Integrated\Bundle\ContentBundle\DependencyInjection\Compiler\TemplatingPass;
use Integrated\Bundle\ContentBundle\DependencyInjection\Compiler\ThemeManagerPass;
use Integrated\Bundle\ContentBundle\DependencyInjection\IntegratedContentExtension;

/**
 * Class IntegratedContentBundle
 *
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class IntegratedContentBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

		$container->addCompilerPass(new ExtensionRegistryBuilderPass());
		$container->addCompilerPass(new FormFactoryEventDispatcherPass());
		$container->addCompilerPass(new MetadataEventDispatcherPass());
		$container->addCompilerPass(new TemplatingPass());
        $container->addCompilerPass(new ThemeManagerPass());
	}

    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        return new IntegratedContentExtension();
    }
}
