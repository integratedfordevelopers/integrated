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

use Serializable;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface UserInterface extends AdvancedUserInterface, GroupableInterface, Serializable
{
    /**
     * @return string
     */
    public function getId();

    /**
     * @param string $username
     */
    public function setUsername($username);

    /**
     * @param string $password
     */
    public function setPassword($password);

    /**
     * @param string $salt
     */
    public function setSalt($salt);

    /**
     * @param string $email
     */
    public function setEmail($email);

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @param RoleInterface $role
     */
    public function addRole(RoleInterface $role);

    /**
     * @param ScopeInterface $scope
     */
    public function setScope(ScopeInterface $scope);

    /**
     * @return ScopeInterface
     */
    public function getScope();
}
