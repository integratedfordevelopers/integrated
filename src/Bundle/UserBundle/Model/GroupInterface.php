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
interface GroupInterface
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
     *     return ['ROLE_USER'];
     * }
     * </code>
     *
     * @return string[]
     */
    public function getRoles();
}
