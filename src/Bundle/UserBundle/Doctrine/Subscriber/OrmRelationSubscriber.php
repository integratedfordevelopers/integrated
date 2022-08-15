<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\Doctrine\Subscriber;

use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Common\EventSubscriber;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Integrated\Bundle\UserBundle\Model\User;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class OrmRelationSubscriber implements EventSubscriber
{
    /**
     * @var ManagerRegistry
     */
    protected $dm;

    public function __construct(ManagerRegistry $dm)
    {
        $this->dm = $dm;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::postLoad,
        ];
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $object = $args->getObject();

        if (!$object instanceof User) {
            return;
        }

        $metadata = $args->getEntityManager()->getClassMetadata(\get_class($object));

        $prop = $metadata->getReflectionClass()->getProperty('relation');
        $prop->setAccessible(true);

        $id = $prop->getValue($object);

        $prop = $metadata->getReflectionClass()->getProperty('relation_instance');
        $prop->setAccessible(true);
        $prop->setValue($object, $this->dm->getManager()->getRepository('Integrated\\Bundle\\ContentBundle\\Document\\Content\\Content')->find($id));
    }
}
