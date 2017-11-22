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
interface GroupManagerInterface extends ManagerInterface
{
	/**
	 * Create a group object
	 *
	 * @return GroupInterface
	 */
	public function create();

	/**
	 * Change or add the group to the manager
	 *
	 * @param GroupInterface $group
	 */
	public function persist(GroupInterface $group);

	/**
	 * Remove the group from the manager
	 *
	 * @param GroupInterface $group
	 */
	public function remove(GroupInterface $group);

	/**
	 * Delete all the managed groups.
	 */
	public function clear();

//	/**
//	 * Return the total number of users
//	 *
//	 * @return int
//	 */
//	public function count();

	/**
	 * Finds the group by its identifier.
	 *
	 * @param mixed $id
	 *
	 * @return GroupInterface
	 */
	public function find($id);

	/**
	 * Finds all the managed groups.
	 *
	 * @return GroupInterface[]
	 */
	public function findAll();

	/**
	 * Finds the group by its name.
	 *
	 * @param string $criteria
	 *
	 * @return GroupInterface
	 */
	public function findByName($criteria);

	/**
	 * Finds the groups by a set of criteria.
	 *
	 * @param array $criteria
	 *
	 * @return GroupInterface[]
	 */
	public function findBy(array $criteria);

	/**
	 * Returns the class name of the group object
	 *
	 * @return string
	 */
	public function getClassName();
}