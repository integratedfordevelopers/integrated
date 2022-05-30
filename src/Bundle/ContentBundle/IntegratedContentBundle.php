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

use Integrated\Bundle\ContentBundle\DependencyInjection\Compiler\ContentProviderPass;
use Integrated\Bundle\ContentBundle\DependencyInjection\Compiler\ContentTypeManagerPass;
use Integrated\Bundle\ContentBundle\DependencyInjection\Compiler\ExtensionRegistryBuilderPass;
use Integrated\Bundle\ContentBundle\DependencyInjection\Compiler\FormFactoryEventDispatcherPass;
use Integrated\Bundle\ContentBundle\DependencyInjection\Compiler\MetadataEventDispatcherPass;
use Integrated\Bundle\ContentBundle\DependencyInjection\Compiler\PriorityResolverBuilderPass;
use Integrated\Bundle\ContentBundle\DependencyInjection\Compiler\SetRouterPass;
use Integrated\Bundle\ContentBundle\DependencyInjection\Compiler\ThemeManagerPass;
use Integrated\Bundle\ContentBundle\DependencyInjection\IntegratedContentExtension;
use Integrated\Common\Bulk\DependencyInjection\ConfigProviderBuilderPass;
use Integrated\Common\Bulk\DependencyInjection\FactoryRegistryBuilderPass;
use Integrated\Common\Normalizer\DependencyInjection\RegistryBuilderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class IntegratedContentBundle.
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

        $container->addCompilerPass(new SetRouterPass());
        $container->addCompilerPass(new ExtensionRegistryBuilderPass());
        $container->addCompilerPass(new FormFactoryEventDispatcherPass());
        $container->addCompilerPass(new MetadataEventDispatcherPass());
        $container->addCompilerPass(new PriorityResolverBuilderPass());
        $container->addCompilerPass(new ContentTypeManagerPass());
        $container->addCompilerPass(new ThemeManagerPass());
        $container->addCompilerPass(new RegistryBuilderPass('integrated_content.json_ld.registry_builder', 'integrated_content.json_ld.processor'));
        $container->addCompilerPass(new FactoryRegistryBuilderPass('integrated_content.bulk.handler_registry_builder', 'integrated_content.bulk.handler'));
        $container->addCompilerPass(new ConfigProviderBuilderPass('integrated_content.bulk.form.chain_provider_builder', 'integrated_content.bulk.form.provider'));
        $container->addCompilerPass(new ContentProviderPass());
    }

    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        return new IntegratedContentExtension();
    }
}
