<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\BlockBundle;

use Integrated\Bundle\BlockBundle\DependencyInjection\Compiler\BlockHandlerRegistryPass;
use Integrated\Bundle\BlockBundle\DependencyInjection\Compiler\ThemeManagerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class IntegratedBlockBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new BlockHandlerRegistryPass());
        $container->addCompilerPass(new ThemeManagerPass());
    }
}
