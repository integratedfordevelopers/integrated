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

//use Serializable;
//use Symfony\Component\Security\Core\Role\Role;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface GroupInterface /* extends Serializable */
{
    /**
     * Returns the identity of the group.
     *
     * @return string
     */
    public function getId();

    /**
     * Set the name of the group.
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * Returns the name of the group.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the roles granted to the group.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * @return \Symfony\Component\Security\Core\Role\Role[] The user roles
     */
    public function getRoles();
}
