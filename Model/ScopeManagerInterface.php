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
 * Interface ScopeManagerInterface
 * @package Integrated\Bundle\UserBundle\Model
 */
interface ScopeManagerInterface extends ManagerInterface
{
    /**
     * Create a group object
     *
     * @return ScopeInterface
     */
    public function create();

    /**
     * Change or add the group to the manager
     *
     * @param ScopeInterface $scope
     */
    public function persist(ScopeInterface $scope);

    /**
     * Remove the group from the manager
     *
     * @param ScopeInterface $scope
     */
    public function remove(ScopeInterface $scope);

    /**
     * Delete all the managed scopes.
     */
    public function clear();

    /**
     * Finds the scope by its identifier.
     *
     * @param mixed $id
     *
     * @return ScopeInterface
     */
    public function find($id);

    /**
     * Finds all the managed scopes.
     *
     * @return ScopeInterface[]
     */
    public function findAll();

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
