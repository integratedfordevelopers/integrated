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
use Doctrine\Persistence\Proxy;
use Doctrine\Persistence\Event\LifecycleEventArgs;
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
        $this->dispatch(Events::PRE_DELETE, $args->getObject());
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $this->dispatch(Events::POST_DELETE, $args->getObject());
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $this->dispatch(Events::PRE_CREATE, $args->getObject());
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->dispatch(Events::POST_CREATE, $args->getObject());
    }

    public function preFlush(LifecycleEventArgs $event)
    {
        $manager = $event->getObjectManager();
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
        $this->dispatch(Events::POST_UPDATE, $args->getObject());
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $this->dispatch(Events::POST_READ, $args->getObject());
    }

    protected function dispatch($event, $object)
    {
        if (($dispatcher = $this->getDispatcher()) === null) {
            return;
        }

        $dispatcher->dispatch($event, $object);
    }
}
