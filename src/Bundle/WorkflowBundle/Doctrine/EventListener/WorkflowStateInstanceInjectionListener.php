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

use Doctrine\Common\EventSubscriber;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Integrated\Bundle\WorkflowBundle\Entity\Workflow\State;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class WorkflowStateInstanceInjectionListener implements EventSubscriber
{
    /**
     * @var ManagerRegistry
     */
    protected $orm;

    /**
     * @var ManagerRegistry
     */
    protected $odm;

    /**
     * @param ManagerRegistry $orm
     * @param ManagerRegistry $odm
     */
    public function __construct(ManagerRegistry $orm, ManagerRegistry $odm)
    {
        $this->orm = $orm;
        $this->odm = $odm;
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

    /**
     * Add the user and content instance or a proxy of the instances to the State
     * entity.
     *
     * @param LifecycleEventArgs $args
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $object = $args->getObject();

        if (!$object instanceof State) {
            return;
        }

        $metadata = $args->getEntityManager()->getClassMetadata(\get_class($object));

        // first the assigned object

        $prop = $metadata->getReflectionClass()->getProperty('assigned_class');
        $prop->setAccessible(true);

        $class = $prop->getValue($object);

        $prop = $metadata->getReflectionClass()->getProperty('assigned_id');
        $prop->setAccessible(true);

        $id = $prop->getValue($object);

        $prop = $metadata->getReflectionClass()->getProperty('assigned_instance');
        $prop->setAccessible(true);
        $prop->setValue($object, $this->getORMInstance($class, $id));

        // now the content object

        $prop = $metadata->getReflectionClass()->getProperty('content_class');
        $prop->setAccessible(true);

        $class = $prop->getValue($object);

        $prop = $metadata->getReflectionClass()->getProperty('content_id');
        $prop->setAccessible(true);

        $id = $prop->getValue($object);

        $prop = $metadata->getReflectionClass()->getProperty('content_instance');
        $prop->setAccessible(true);
        $prop->setValue($object, $this->getODMInstance($class, $id));
    }

    /**
     * Try to get a reference to the user object else fetch it immediately from the
     * repository.
     *
     * @param string $class
     * @param string $id
     *
     * @return object
     */
    protected function getORMInstance($class, $id)
    {
        if (!$class || !$id) {
            return null;
        }

        $manager = $this->orm->getManagerForClass($class);

        if (method_exists($manager, 'getReference')) {
            return $manager->getReference($class, $id);
        }

        return $manager->getRepository($class)->find($id);
    }

    /**
     * Try to get a reference to the content object else fetch it immediately from the
     * repository.
     *
     * @param string $class
     * @param string $id
     *
     * @return object
     */
    public function getODMInstance($class, $id)
    {
        $manager = $this->odm->getManagerForClass($class);

        if (method_exists($manager, 'getReference')) {
            return $manager->getReference($class, $id);
        }

        return $manager->getRepository($class)->find($id);
    }
}
