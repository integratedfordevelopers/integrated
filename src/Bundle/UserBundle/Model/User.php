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
class User implements UserInterface
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var null | string
     */
    protected $salt = null;

    /**
     * @var null | string
     */
    protected $email = null;

    /**
     * @var \DateTimeInterface
     */
    protected $createdAt;

    /**
     * @var Collection | GroupInterface[]
     */
    protected $groups;

    /**
     * @var Collection | RoleInterface[]
     */
    protected $roles = [];

    /**
     * @var bool
     */
    protected $locked = false;

    /**
     * @var bool
     */
    protected $enabled = true;

    /**
     * @var string
     */
    protected $relation = null;

    /**
     * @var Scope
     */
    protected $scope;

    /**
     * @var \Integrated\Bundle\ContentBundle\Document\Content\Relation\Relation
     */
    protected $relation_instance = null;

    public function __construct()
    {
        $this->groups = new ArrayCollection();
        $this->roles = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->username,
            $this->password,
            $this->salt,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        list(
            $this->id,
            $this->username,
            $this->password,
            $this->salt) = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setUsername($username)
    {
        $this->username = (string) $username;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function setPassword($password)
    {
        $this->password = (string) $password;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * {@inheritdoc}
     */
    public function setSalt($salt)
    {
        $this->salt = $salt !== null ? (string) $salt : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * {@inheritdoc}
     */
    public function setEmail($email)
    {
        $this->email = $email !== null ? (string) $email : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }


    /**
     * @param GroupInterface $group
     */
    public function addGroup(GroupInterface $group)
    {
        if (!$this->groups->contains($group)) {
            $this->groups->add($group);
        }
    }

    /**
     * @param GroupInterface $group
     */
    public function removeGroup(GroupInterface $group)
    {
        $this->groups->removeElement($group);
    }

    /**
     * @param GroupInterface $group
     *
     * @return bool
     */
    public function hasGroup(GroupInterface $group)
    {
        return $this->groups->contains($group);
    }

    /**
     * {@inheritdoc}
     */
    public function getGroups()
    {
        return $this->groups->toArray();
    }

    /**
     * @param GroupInterface[] $groups
     */
    public function setGroups($groups)
    {
        $this->groups = new ArrayCollection();

        foreach ($groups as $group) {
            $this->addGroup($group);
        }
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
     * @param RoleInterface $role
     */
    public function removeRole(RoleInterface $role)
    {
        $this->roles->removeElement($role);
    }

    /**
     * @param RoleInterface $role
     *
     * @return bool
     */
    public function hasRole(RoleInterface $role)
    {
        return $this->roles->contains($role);
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        $roles = [];

        if ($this->enabled) {
            $roles[] = 'ROLE_USER'; // Every user must have this role
        }

        foreach ($this->roles as $role) {
            $roles[] = $role->getRole();
        }

        foreach ($this->getGroups() as $group) {
            $roles = array_merge($roles, $group->getRoles());
        }

        return array_unique($roles);
    }

    /**
     * @param bool $locked
     */
    public function setLocked($locked = true)
    {
        $this->locked = (bool) $locked;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled($enabled = true)
    {
        $this->enabled = (bool) $enabled;
    }

    /**
     * @param \Integrated\Bundle\ContentBundle\Document\Content\Relation\Relation $relation
     */
    public function setRelation($relation = null)
    {
        $relation = $relation instanceof \Integrated\Bundle\ContentBundle\Document\Content\Relation\Relation ? $relation : null;

        $this->relation = $relation ? $relation->getId() : null;
        $this->relation_instance = $relation;
    }

    /**
     * @return \Integrated\Bundle\ContentBundle\Document\Content\Relation\Relation
     */
    public function getRelation()
    {
        return $this->relation_instance;
    }

    /**
     * @return ScopeInterface
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * @param ScopeInterface $scope
     *
     * @return $this
     */
    public function setScope(ScopeInterface $scope)
    {
        $this->scope = $scope;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonExpired()
    {
        return true; // @todo implement
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonLocked()
    {
        return !$this->locked;
    }

    /**
     * {@inheritdoc}
     */
    public function isCredentialsNonExpired()
    {
        return true; // @todo implement
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
        /* do nothing as there are no unsecured credentials, password should be encrypted */
    }

    /**
     * Get the string representation of the user object.
     *
     * This can be useful for debugging
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf(
            "ID: %s\nUsername: %s\n CreatedAt: %s\nEnabled: %s\nLocked: %s\nExpired (account): %s\nExpired (credentials): %s",
            $this->getId(),
            $this->getUsername(),
            $this->getCreatedAt(),
            $this->isEnabled() ? 'TRUE' : 'FALSE',
            $this->isAccountNonLocked() ? 'FALSE' : 'TRUE',
            $this->isAccountNonExpired() ? 'FALSE' : 'TRUE',
            $this->isCredentialsNonExpired() ? 'FALSE' : 'TRUE'
        );
    }
}
