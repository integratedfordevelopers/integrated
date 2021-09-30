<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content\Extension\Adaptor\Doctrine;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Proxy;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Integrated\Common\Content\Extension\Adaptor\AbstractAdaptor;
use Integrated\Common\Content\Extension\Events;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class DoctrineOrmAdaptor extends AbstractAdaptor implements EventSubscriber
{
    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            'preRemove',
            'postRemove',
            'prePersist',
            'postPersist',
            'preFlush', // calculate our of preUpdate
            'postUpdate',
            'postLoad',
        ];
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $this->dispatch(Events::PRE_DELETE, $args->getEntity());
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $this->dispatch(Events::POST_DELETE, $args->getEntity());
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $this->dispatch(Events::PRE_CREATE, $args->getEntity());
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->dispatch(Events::POST_CREATE, $args->getEntity());
    }

    public function preFlush(PreFlushEventArgs $event)
    {
        $manager = $event->getEntityManager();
        $uow = $manager->getUnitOfWork();

        foreach ($uow->getIdentityMap() as $class => $objects) {
            $class = $manager->getClassMetadata($class);

            if ($class->isReadOnly) {
                continue;
            }

            foreach ($objects as $object) {
                if ($object instanceof Proxy && !$object->__isInitialized()) {
                    continue;
                }

                if ($uow->isScheduledForInsert($object) || $uow->isScheduledForDelete($object)) {
                    continue;
                }

                $this->dispatch(Events::PRE_UPDATE, $object);
            }
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->dispatch(Events::POST_UPDATE, $args->getEntity());
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $this->dispatch(Events::POST_READ, $args->getEntity());
    }

    protected function dispatch($event, $object)
    {
        if (($dispatcher = $this->getDispatcher()) === null) {
            return;
        }

        $dispatcher->dispatch($event, $object);
    }
}
