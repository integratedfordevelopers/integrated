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
     * @param ContainerInterface       $container
     * @param string                   $manager
     * @param string                   $filesystemRemove
     * @param string                   $intentTransformer
     */
    public function __construct(ContainerInterface $container, $manager, $filesystemRemove, $intentTransformer)
    {
        $this->initializer = function () use ($container, $manager, $filesystemRemove, $intentTransformer) {
            parent::__construct(
                $container->get($manager),
                $container->get($filesystemRemove),
                $container->get($intentTransformer)
            );

            $this->initializer = null;
        };
    }

    /**
     * {@inheritDoc}
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->initializer && $this->initializer->__invoke();

        parent::prePersist($args);
    }

    /**
     * {@inheritDoc}
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $this->initializer && $this->initializer->__invoke();

        parent::preRemove($args);
    }

    /**
     * {@inheritDoc}
     */
    public function preFlush(PreFlushEventArgs $args)
    {
        $this->initializer && $this->initializer->__invoke();

        parent::preFlush($args);
    }

    /**
     * {@inheritDoc}
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $this->initializer && $this->initializer->__invoke();

        parent::onFlush($args);
    }
}
