<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class EventDispatcherPass implements CompilerPassInterface
{
    const TAG = 'integrated_content.event';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $dispatcher = $container->getDefinition('integrated_content.event_dispatcher');

        foreach ($container->findTaggedServiceIds(self::TAG) as $service => $tags) {
            $dispatcher->addMethodCall('addSubscriber', [$container->getDefinition($service)]);
        }
    }
}
