<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentHistoryBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class WorkflowSubscriberPass implements CompilerPassInterface
{
    /**
     * @inheritdoc
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('integrated_workflow.extension')) {
            return; // workflow is optional
        }

        $subscriber = new Definition('%integrated_content_history.event_listener.workflow_subscriber.class%');
        $subscriber->addTag('integrated.content_history.event_subscriber');

        $container->setDefinition('integrated_content_history.event_listener.workflow_subscriber', $subscriber);

        $dispatcher = $container->getDefinition('integrated_content_history.event_dispatcher');
        $dispatcher->addMethodCall('addSubscriber', [$subscriber]);
    }
}
