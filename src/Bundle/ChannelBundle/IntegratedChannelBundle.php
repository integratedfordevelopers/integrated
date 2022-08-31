<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ChannelBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Integrated\Bundle\ChannelBundle\DependencyInjection\Compiler\RegisterAdapterPass;
use Integrated\Bundle\ChannelBundle\DependencyInjection\Compiler\RegisterConfigPass;
use Integrated\Bundle\ChannelBundle\DependencyInjection\Compiler\RegisterConfigResolverPass;
use Integrated\Bundle\ChannelBundle\DependencyInjection\IntegratedChannelExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class IntegratedChannelBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(
            DoctrineOrmMappingsPass::createXmlMappingDriver(
                [__DIR__.'/Resources/config/model' => 'Integrated\Bundle\ChannelBundle\Model']
            )
        );

        $container->addCompilerPass(new RegisterConfigPass());
        $container->addCompilerPass(new RegisterConfigResolverPass());
        $container->addCompilerPass(new RegisterAdapterPass());
    }

    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        return new IntegratedChannelExtension();
    }
}
