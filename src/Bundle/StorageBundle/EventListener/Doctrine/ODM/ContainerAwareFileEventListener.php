<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\EventListener\Doctrine\ODM;

use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Event\OnFlushEventArgs;
use Doctrine\ODM\MongoDB\Event\PreFlushEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ContainerAwareFileEventListener extends FileEventListener
{
    /**
     * @var \Closure | null
     */
    private $initializer;

    /**
     * @param ContainerInterface $container
     * @param string             $manager
     * @param string             $intentTransformer
     */
    public function __construct(ContainerInterface $container, $manager, $intentTransformer)
    {
        $this->initializer = function () use ($container, $manager, $intentTransformer) {
            parent::__construct(
                $container->get($manager),
                $container->get($intentTransformer)
            );

            $this->initializer = null;
        };
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->initializer && $this->initializer->__invoke();

        parent::prePersist($args);
    }

    /**
     * {@inheritdoc}
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $this->initializer && $this->initializer->__invoke();
    }

    /**
     * {@inheritdoc}
     */
    public function preFlush(PreFlushEventArgs $args)
    {
        $this->initializer && $this->initializer->__invoke();

        parent::preFlush($args);
    }

    /**
     * {@inheritdoc}
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $this->initializer && $this->initializer->__invoke();
    }
}
