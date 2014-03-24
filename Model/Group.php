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
	 * @var string[]
	 */
	protected $roles = array();

	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param string $name
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
	 * @return string[]
	 */
	public function getRoles()
	{
		return $this->roles;
	}

	/**
	 * @param array $roles
	 */
	public function setRoles(array $roles)
	{
		$this->roles = array_unique(array_map('strtoupper', array_filter($roles)));
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
} 