<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WorkflowBundle\Doctrine\EventListener;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ContainerAwareQueueListener extends QueueListener
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var string
     */
    protected $service;

    /**
     * @param ContainerInterface $container
     * @param string             $service   the name of the queue service to use
     */
    public function __construct(ContainerInterface $container, $service)
    {
        $this->container = $container;
        $this->service = $service;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueue()
    {
        $queue = parent::getQueue();

        if (null === $queue) {
            $queue = $this->container->get($this->service);

            $this->setQueue($queue);
        }

        return $queue;
    }
}
