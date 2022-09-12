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
use Integrated\Common\Security\Permission as CommonPermission;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Permission extends CommonPermission
{
    /**
     * @var State
     */
    protected $state;

    /**
     * @param State $state
     *
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
     * Remove the permissions that have a null state (orphans).
     */
    public function doPermissionFix(PreFlushEventArgs $event)
    {
        if ($this->getState() === null) {
            $uow = $event->getObjectManager()->getUnitOfWork();

            // this entity should always be in the identity map or else this event should not be
            // triggered. But still check it anyways in case someone, for some unknown reasons,
            // triggers this callback manually.

            if ($uow->isInIdentityMap($this)) {
                $uow->scheduleOrphanRemoval($this);
            }
        }
    }
}
