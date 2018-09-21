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
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Integrated\Bundle\WorkflowBundle\Entity\Workflow\Log;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class WorkflowLogInstanceInjectionListener implements EventSubscriber
{
    /**
     * @var ManagerRegistry
     */
    protected $manager;

    /**
     * @param ManagerRegistry $manager
     */
    public function __construct(ManagerRegistry $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::postLoad,
        ];
    }

    /**
     * Add the user instance or a proxy to this user instance to the Log entity.
     *
     * @param LifecycleEventArgs $args
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $object = $args->getEntity();

        if (!$object instanceof Log) {
            return;
        }

        $metadata = $args->getEntityManager()->getClassMetadata(\get_class($object));

        $prop = $metadata->getReflectionClass()->getProperty('user_class');
        $prop->setAccessible(true);

        $class = $prop->getValue($object);

        $prop = $metadata->getReflectionClass()->getProperty('user_id');
        $prop->setAccessible(true);

        $id = $prop->getValue($object);

        $prop = $metadata->getReflectionClass()->getProperty('user_instance');
        $prop->setAccessible(true);
        $prop->setValue($object, $this->getInstance($class, $id));
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
    protected function getInstance($class, $id)
    {
        if (!$class || !$id) {
            return null;
        }

        $manager = $this->manager->getManagerForClass($class);

        if (method_exists($manager, 'getReference')) {
            return $manager->getReference($class, $id);
        }

        return $manager->getRepository($class)->find($id);
    }
}
