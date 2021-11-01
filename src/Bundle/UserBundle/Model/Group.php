<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Group implements GroupInterface
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var Collection|RoleInterface[]
     */
    protected $roles;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param RoleInterface $role
     */
    public function addRole(RoleInterface $role)
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
        }
    }

    /**
     * @param RoleInterface|string $role
     */
    public function removeRole($role)
    {
        if ($role instanceof RoleInterface) {
            $this->roles->removeElement($role);
        }

        foreach ($this->roles as $object) {
            if ($role === $object->getRole()) {
                $this->roles->removeElement($object);
                break;
            }
        }
    }

    /**
     * @param RoleInterface|string $role
     *
     * @return bool
     */
    public function hasRole($role)
    {
        if ($role instanceof RoleInterface) {
            return $this->roles->contains($role);
        }

        foreach ($this->roles as $object) {
            if ($role === $object->getRole()) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        $roles = [];

        foreach ($this->roles as $role) {
            $roles[] = $role->getRole();
        }

        return $roles;
    }

    /**
     * Get the string representation of the group object.
     *
     * This can be use full for debugging
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf(
            "ID: %s\nGroup: %s",
            $this->getId(),
            $this->getName()
        );
    }
}
