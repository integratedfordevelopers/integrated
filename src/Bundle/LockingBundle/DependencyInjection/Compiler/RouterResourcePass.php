<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\LockingBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Yaml;

/**
 * Configuration class for ContentBundle.
 *
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class RouterResourcePass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $enabled = $container->getParameterBag()->resolveValue($container->getParameter('router.resource'));

        if (!$enabled) {
            return;
        }

        $file = $container->getParameter('kernel.cache_dir').'/locking/routing.yml';

        if (!is_dir($dir = \dirname($file))) {
            mkdir($dir, 0777, true);
        }

        file_put_contents($file, Yaml::dump([
            '_integrated_locking_api' => ['resource' => '@IntegratedLockingBundle/Resources/config/routing.xml'],
            '_integrated_locking' => ['resource' => $container->getParameter('router.resource')],
        ]));

        $container->setParameter('router.resource', $file);
    }
}
