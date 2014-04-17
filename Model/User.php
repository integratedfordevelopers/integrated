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

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

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
	 * @var string[]
	 */
	protected $roles = array();

	/**
	 * @var Collection | GroupInterface[]
	 */
	protected $groups;

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
	 * @var \Integrated\Bundle\ContentBundle\Document\Content\Relation\Relation
	 */
	protected $relation_instance = null;

	public function __construct()
	{
		$this->groups = new ArrayCollection();
	}

	/**
	 * @inheritdoc
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
	 * @inheritdoc
	 */
	public function unserialize($serialized)
	{
		$data = unserialize($serialized);

		list(
			$this->id,
			$this->username,
			$this->password,
			$this->salt,
		) = $data;
	}

	/**
	 * @inheritdoc
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param string $username
	 */
	public function setUsername($username)
	{
		$this->username = $username;
	}

	/**
	 * @inheritdoc
	 */
	public function getUsername()
	{
		return $this->username;
	}

	/**
	 * @param string $password
	 */
	public function setPassword($password)
	{
		$this->password = $password;
	}

	/**
	 * @inheritdoc
	 */
	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * @param string $salt
	 */
	public function setSalt($salt)
	{
		$this->salt = $salt;
	}

	/**
	 * @inheritdoc
	 */
	public function getSalt()
	{
		return $this->salt;
	}

	/**
	 * @param string $email
	 */
	public function setEmail($email)
	{
		$this->email = $email;
	}

	/**
	 * @inheritdoc
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * @param string $role
	 */
	public function addRole($role)
	{
		if (!$this->hasRole($role)) {
			$this->roles[] = strtoupper($role);
		}
	}

	/**
	 * @param string $role
	 */
	public function removeRole($role)
	{
		if ($key = array_search(strtoupper($role), $this->roles) !== false) {
			unset($this->roles[$key]);
			$this->roles = array_values($this->roles);
		}
	}

	/**
	 * @param $role
	 * @return bool
	 */
	public function hasRole($role)
	{
		return in_array(strtoupper($role), $this->roles);
	}

	/**
	 * @inheritdoc
	 */
	public function getRoles()
	{
		$roles = $this->roles;

		foreach ($this->getGroups() as $group) {
			$roles = array_merge($roles, $group->getRoles());
		}

		return array_unique($roles);
	}

	/**
	 * @param array $roles
	 */
	public function setRoles(array $roles)
	{
		$this->roles = array();

		foreach ($roles as $role) {
			$this->addRole($role);
		}
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
	 * @return bool
	 */
	public function hasGroup(GroupInterface $group)
	{
		return $this->groups->contains($group);
	}

	/**
	 * @inheritdoc
	 */
	public function getGroups()
	{
		return $this->groups;
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
//		try	{
//			if ($this->relation_instance instanceof \Doctrine\Common\Persistence\Proxy && !$this->relation_instance->__isInitialized()) {
//				$this->relation_instance->__load();
//			}
//		} catch (\Exception $e) {
//			$this->relation_instance = null;
//		}

		return $this->relation_instance;
	}

	/**
	 * @inheritdoc
	 */
	public function isAccountNonExpired()
	{
		return true; // @todo implement
	}

	/**
	 * @inheritdoc
	 */
	public function isAccountNonLocked()
	{
		return !$this->locked;
	}

	/**
	 * @inheritdoc
	 */
	public function isCredentialsNonExpired()
	{
		return true; // @todo implement
	}

	/**
	 * @inheritdoc
	 */
	public function isEnabled()
	{
		return $this->enabled;
	}

	/**
	 * @inheritdoc
	 */
	public function eraseCredentials() { /* do nothing as there are no unsecured credentials, password should be encrypted */ }

	public function __toString()
	{
		return (string) $this->username;
	}
}