<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\LockingBundle;

use Integrated\Bundle\LockingBundle\DependencyInjection\Compiler\RouterResourcePass;
use Integrated\Bundle\LockingBundle\DependencyInjection\IntegratedLockingExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class IntegratedLockingBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RouterResourcePass(), 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        return new IntegratedLockingExtension();
    }
}
