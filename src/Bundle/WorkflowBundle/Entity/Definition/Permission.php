<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WorkflowBundle\Entity\Definition;

use Doctrine\ORM\Event\PreFlushEventArgs;
use Integrated\Bundle\UserBundle\Model\GroupInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Permission
{
	const READ  = 1;
	const WRITE = 2;

	/**
	 * @var State
	 */
	protected $state;

	/**
	 * @var string
	 */
	protected $group;

	/**
	 * @var int
	 */
	protected $mask;

	/**
	 * @param State $state
	 * @return $this
	 */
	public function setState(State $state = null)
	{
		if ($this->state !== $state && $this->state !== null) {
			$this->state->removePermission($this);
		}

		$this->state = $state;

		if ($this->state) {
			$this->state->addPermission($this);
		}

		return $this;
	}

	/**
	 * @return State
	 */
	public function getState()
	{
		return $this->state;
	}

	/**
	 * @param string | GroupInterface $group
	 * @return $this
	 */
	public function setGroup($group)
	{
		if ($group instanceof GroupInterface) {
			$group = $group->getId();
		}

		$this->group = (string) $group;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getGroup()
	{
		return $this->group;
	}

	/**
	 * @param int $mask
	 * @return $this
	 */
	public function setMask($mask)
	{
		$this->mask = (int) $mask;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getMask()
	{
		return $this->mask;
	}

	/**
	 * @param int $mask
	 * @return $this
	 */
	public function addMask($mask)
	{
		$this->mask = $this->mask | intval($mask);
		return $this;
	}

	/**
	 * @param int $mask
	 * @return $this
	 */
	public function removeMask($mask)
	{
		$this->mask = $this->mask - ($this->mask & intval($mask));
		return $this;
	}

	/**
	 * @param int $mask
	 * @return bool
	 */
	public function hasMask($mask)
	{
		return (bool) ($this->mask & $mask) == $mask;
	}

	/**
	 * Remove the permissions that have a null state (orphans)
	 *
	 * @param PreFlushEventArgs $event
	 */
	public function doPermissionFix(PreFlushEventArgs $event)
	{
		if ($this->getState() === null) {
			$uow = $event->getEntityManager()->getUnitOfWork();

			// this entity should always be in the identity map or else this event should not be
			// triggered. But still check it anyways in case someone, for some unknown reasons,
			// triggers this callback manually.

			if ($uow->isInIdentityMap($this)) {
				$uow->scheduleOrphanRemoval($this);
			}
		}
	}
}