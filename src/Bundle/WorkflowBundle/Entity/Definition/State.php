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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\PersistentCollection;
use Integrated\Bundle\WorkflowBundle\Entity\Definition;
use Integrated\Bundle\WorkflowBundle\Utils\StateVisibleConfig;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class State
{
    /**
     * @var int
     */
    protected $id = null;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var Definition|null
     */
    protected $workflow = null;

    /**
     * @var int
     */
    protected $order = 0;

    /**
     * @var bool
     */
    protected $publishable = false;

    /**
     * @var Collection|Permission[]
     */
    protected $permissions;

    /**
     * @var Collection|State[]
     */
    protected $transitions;

    /**
     * @var int
     */
    protected $comment = StateVisibleConfig::OPTIONAL;

    /**
     * @var int
     */
    protected $assignee = StateVisibleConfig::OPTIONAL;

    /**
     * @var int
     */
    protected $deadline = StateVisibleConfig::OPTIONAL;

    /**
     * @return int
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param int $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return int
     */
    public function getAssignee()
    {
        return $this->assignee;
    }

    /**
     * @param int $assignee
     */
    public function setAssignee($assignee)
    {
        $this->assignee = $assignee;
    }

    /**
     * @return int
     */
    public function getDeadline()
    {
        return $this->deadline;
    }

    /**
     * @param int $deadline
     */
    public function setDeadline($deadline)
    {
        $this->deadline = $deadline;
    }

    public function __construct()
    {
        $this->permissions = new ArrayCollection();
        $this->transitions = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = (string) $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param Definition|null $workflow
     *
     * @return $this
     */
    public function setWorkflow(Definition $workflow = null)
    {
        if ($this->workflow !== $workflow && $this->workflow !== null) {
            $this->workflow->removeState($this);
        }

        $this->workflow = $workflow;

        if ($this->workflow) {
            $this->workflow->addState($this);
        }

        return $this;
    }

    /**
     * @return Definition|null
     */
    public function getWorkflow()
    {
        return $this->workflow;
    }

    /**
     * @param int $order
     *
     * @return $this
     */
    public function setOrder($order)
    {
        $this->order = (int) $order;

        return $this;
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param bool $publish
     *
     * @return $this
     */
    public function setPublishable($publishable)
    {
        $this->publishable = (bool) $publishable;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPublishable()
    {
        return $this->publishable;
    }

    /**
     * @param Permission[] $permissions
     *
     * @return $this
     */
    public function setPermissions(Collection $permissions)
    {
        foreach ($this->permissions as $permission) {
            $this->removePermission($permission);
        }

        foreach ($permissions as $permission) {
            $this->addPermission($permission);
        }

        return $this;
    }

    /**
     * @return Permission[]
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * @param Permission $permission
     *
     * @return $this
     */
    public function addPermission(Permission $permission)
    {
        if (!$this->permissions->contains($permission)) {
            $this->permissions->add($permission);

            // first add the permission to the state then set the state else
            // there would be a infinite loop

            $permission->setState($this);
        }

        return $this;
    }

    /**
     * @param Permission $permission
     *
     * @return $this
     */
    public function removePermission(Permission $permission)
    {
        if ($this->permissions->removeElement($permission)) {
            $permission->setState(null);
        }

        return $this;
    }

    /**
     * @param Collection $transitions
     *
     * @return $this
     */
    public function setTransitions(Collection $transitions)
    {
        $this->transitions->clear();
        $this->transitions = new ArrayCollection();

        foreach ($transitions as $transition) {
            $this->addTransition($transition); // type check
        }

        return $this;
    }

    /**
     * @return State[]
     */
    public function getTransitions()
    {
        return $this->transitions->toArray();
    }

    /**
     * @param State $state
     *
     * @return $this
     */
    public function addTransition(self $state)
    {
        if (!$this->transitions->contains($state)) {
            $this->transitions->add($state);
        }

        return $this;
    }

    /**
     * @param State $state
     *
     * @return $this
     */
    public function removeTransition(self $state)
    {
        $this->transitions->removeElement($state);

        return $this;
    }

    /**
     * @return bool
     */
    public function isDefault()
    {
        if (isset($this->workflow)) {
            return $this === $this->workflow->getDefault();
        }

        return false;
    }

    /**
     * Fix issues with primary key constraints errors because deletes are execute
     * after updates and inserts.
     *
     * @param PreFlushEventArgs $event
     */
    public function doPermissionFix(PreFlushEventArgs $event)
    {
        // if not a PersistentCollection then its probably is a new entity else check if
        // data from the database is loaded or not.

        if (!$this->permissions instanceof PersistentCollection || !$this->permissions->isInitialized()) {
            return;
        }

        $em = $event->getEntityManager();
        $uow = $em->getUnitOfWork();

        /** @var Permission $permission */
        /* @var Permission $found */

        foreach ($this->permissions as $permission) {
            // see if there is already a entity in de identity map with this primary key. If so
            // then use that one and removed the one in the collection from the identity map. But
            // only when the state is null or a entity matching $this else the permission is
            // moved to an other state. (could give a problem if inserts are done before updates)
            //
            // NOTE: This also means that all the changes to the entity that is removed from
            // the collection wont be recorded by doctrine anymore.

            if ($found = $uow->tryGetById([$permission->getGroup(), $this->getId()], \get_class($permission))) {
                if ($found !== $permission && ($found->getState() === null || $found->getState() === $this)) {
                    $this->permissions->removeElement($permission);
                    $this->permissions->add($found);

                    if ($uow->isInIdentityMap($permission)) {
                        $uow->detach($permission);
                    }

                    $found->setState($this);
                    $found->setMask($permission->getMask());

                    $uow->persist($found);
                }
            }
        }
    }
}
