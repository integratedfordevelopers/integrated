<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WebsiteBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class ThemeManagerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('integrated_theme.templating.theme_manager')) {
            return;
        }

        $definition = $container->getDefinition('integrated_theme.templating.theme_manager');

        $definition->addMethodCall(
            'registerPath',
            ['default', '@IntegratedWebsite/themes/default']
        );
    }
}
