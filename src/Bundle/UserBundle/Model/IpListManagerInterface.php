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

use Darsyn\IP\Version\Multi as IP;

interface IpListManagerInterface extends ManagerInterface
{
    /**
     * Create a ip list object.
     *
     * @return IpList
     */
    public function create(IP $ip, string $description);

    /**
     * Change or add the ip list to the manager.
     *
     * @param IpList $scope
     */
    public function persist(IpList $scope);

    /**
     * Remove the ip list from the manager.
     *
     * @param IpList $scope
     */
    public function remove(IpList $scope);

    /**
     * Delete all the managed ip lists.
     */
    public function clear();

    /**
     * Finds the ip list by its identifier.
     *
     * @param mixed $id
     *
     * @return IpList
     */
    public function find($id);

    /**
     * Finds all the managed ip lists.
     *
     * @return IpList[]
     */
    public function findAll();

    /**
     * Finds the ip lists by a set of criteria.
     *
     * @param array $criteria
     *
     * @return IpList[]
     */
    public function findBy(array $criteria);

    /**
     * Returns the class name of the ip list object.
     *
     * @return string
     */
    public function getClassName();
}
