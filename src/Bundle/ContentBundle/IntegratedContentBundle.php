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

use Integrated\Bundle\ContentBundle\DependencyInjection\Compiler\BraincraftedFlashMessagePass;
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
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
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

        $container->addCompilerPass(new SetRouterPass(), 0);
        $container->addCompilerPass(new ExtensionRegistryBuilderPass(), 0);
        $container->addCompilerPass(new FormFactoryEventDispatcherPass(), 0);
        $container->addCompilerPass(new MetadataEventDispatcherPass(), 0);
        $container->addCompilerPass(new PriorityResolverBuilderPass(), 0);
        $container->addCompilerPass(new ContentTypeManagerPass(), 0);
        $container->addCompilerPass(new ThemeManagerPass(), 0);
        $container->addCompilerPass(new RegistryBuilderPass('integrated_content.json_ld.registry_builder', 'integrated_content.json_ld.processor'), 0);
        $container->addCompilerPass(new FactoryRegistryBuilderPass('integrated_content.bulk.handler_registry_builder', 'integrated_content.bulk.handler'), 0);
        $container->addCompilerPass(new ConfigProviderBuilderPass('integrated_content.bulk.form.chain_provider_builder', 'integrated_content.bulk.form.provider'), 0);
        $container->addCompilerPass(new ContentProviderPass(), 0);
        $container->addCompilerPass(new BraincraftedFlashMessagePass(), 0);

        $container->addCompilerPass(new RegisterListenersPass(
            'integrated_content.event_dispatcher',
            'integrated_content.event_listener',
            'integrated_content.event_subscriber'
        ), 0);

        $container->addCompilerPass(new RegisterListenersPass(
            'integrated_content.form_block.event_dispatcher',
            'integrated_content.form_block.event_listener',
            'integrated_content.form_block.event_subscriber'
        ), 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        return new IntegratedContentExtension();
    }
}
