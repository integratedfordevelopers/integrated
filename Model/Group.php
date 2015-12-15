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
	 * @var Collection | RoleInterface[]
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
	 * @inheritdoc
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @inheritdoc
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param $role
	 */
	public function addRole($role)
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
	 * @return bool
	 */
	public function hasRole(RoleInterface $role)
	{
		return $this->roles->contains($role);
	}

	/**
	 * @inheritdoc
	 */
	public function getRoles()
	{
		return $this->roles->toArray();
	}

//	/**
//	 * @inheritdoc
//	 */
//	public function serialize()
//	{
//		// TODO: Implement serialize() method.
//	}
//
//	/**
//	 * @inheritdoc
//	 */
//	public function unserialize($serialized)
//	{
//		// TODO: Implement unserialize() method.
//	}


	/**
	 * Get the string representation of the group object.
	 *
	 * This can be use full for debugging
	 *
	 * @return string
	 */
	public function __toString()
	{
		return sprintf("ID: %s\nGroup: %s",
			$this->getId(),
			$this->getName()
		);
	}
} 