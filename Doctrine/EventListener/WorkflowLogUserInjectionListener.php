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
class WorkflowLogUserInjectionListener implements EventSubscriber
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
			Events::postLoad
		];
	}

	public function postLoad(LifecycleEventArgs $args)
	{
		$object = $args->getEntity();

		if (!$object instanceof Log) {
			return;
		}

		$metadata = $args->getEntityManager()->getClassMetadata(get_class($object));

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

	protected function getInstance($class, $id)
	{
		$manager = $this->dm->getManagerForClass($class);

		if (method_exists($manager, 'getReference')) {
			return $manager->getReference($class, $id);
		}

		return $manager->getRepository($class)->find($id);
	}
}