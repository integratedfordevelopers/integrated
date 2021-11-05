<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Integrated\Bundle\UserBundle\DependencyInjection\Compiler\RegisterRolesParametersPass;
use Integrated\Bundle\UserBundle\DependencyInjection\IntegratedUserExtension;
use Integrated\Bundle\UserBundle\DependencyInjection\Security\IpListFactory;
use Integrated\Bundle\UserBundle\DependencyInjection\Security\ScopeFactory;
use Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class IntegratedUserBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        $mapping = [
            __DIR__.'/Resources/config/mapping/doctrine/' => 'Integrated\\Bundle\\UserBundle\\Model',
        ];

        $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($mapping, ['integrated_user.mapping.entity_manager'], 'integrated_user.mapping.enabled'), 0);
        $container->addCompilerPass(new RegisterRolesParametersPass(), 0);

        $security = $container->getExtension('security');

        if ($security instanceof SecurityExtension) {
            $security->addSecurityListenerFactory(new ScopeFactory());
            $security->addSecurityListenerFactory(new IpListFactory());
        }
    }

    /**
     * @return IntegratedUserExtension
     */
    public function getContainerExtension()
    {
        return new IntegratedUserExtension();
    }
}
