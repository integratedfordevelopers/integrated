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

use Countable;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface ManagerInterface /* extends Countable */
{
    /**
     * Delete all the managed objects.
     */
    public function clear();

    /**
     * Finds the object by its identifier.
     *
     * @param mixed $id
     *
     * @return object
     */
    public function find($id);

    /**
     * Finds all the managed users.
     *
     * @return object[]
     */
    public function findAll();

    /**
     * Finds the objects by a set of criteria.
     *
     * @param array $criteria
     *
     * @return object[]
     */
    public function findBy(array $criteria);

    /**
     * Returns the class name of the managed object.
     *
     * @return string
     */
    public function getClassName();
}
