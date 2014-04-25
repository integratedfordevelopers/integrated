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
interface RoleManagerInterface extends ManagerInterface
{
	/**
	 * Create a role object
	 *
	 * @param string $role
	 * @return RoleInterface
	 */
	public function create($role);

	/**
	 * Change or add the role to the manager
	 *
	 * @param RoleInterface $role
	 */
	public function persist(RoleInterface $role);

	/**
	 * Remove the role from the manager
	 *
	 * @param RoleInterface $role
	 */
	public function remove(RoleInterface $role);

	/**
	 * Delete all the managed roles.
	 */
	public function clear();

//	/**
//	 * Return the total number of roles
//	 *
//	 * @return int
//	 */
//	public function count();

	/**
	 * Finds the role by its identifier.
	 *
	 * @param mixed $id
	 *
	 * @return RoleInterface
	 */
	public function find($id);

	/**
	 * Finds all the managed role.
	 *
	 * @return RoleInterface[]
	 */
	public function findAll();

	/**
	 * Finds the role by its name.
	 *
	 * @param string $criteria
	 *
	 * @return RoleInterface
	 */
	public function findByName($criteria);

	/**
	 * Finds the roles by a set of criteria.
	 *
	 * @param array $criteria
	 *
	 * @return RoleInterface[]
	 */
	public function findBy(array $criteria);

	/**
	 * Returns the class name of the role object
	 *
	 * @return string
	 */
	public function getClassName();
}