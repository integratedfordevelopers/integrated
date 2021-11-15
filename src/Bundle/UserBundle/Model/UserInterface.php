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

use Scheb\TwoFactorBundle\Model\Google\TwoFactorInterface;
use Serializable;
use Symfony\Component\Security\Core\User\EquatableInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface UserInterface extends GroupableInterface, TwoFactorInterface, Serializable, EquatableInterface
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
     * Checks whether the user is enabled.
     */
    public function isEnabled(): bool;

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

    public function setGoogleAuthenticatorEnabled(bool $googleAuthenticatorEnabled): void;

    public function setGoogleAuthenticatorSecret(?string $googleAuthenticatorSecret): void;
}
