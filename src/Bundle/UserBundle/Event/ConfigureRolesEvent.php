<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * @author Vasil Pascal <developer.optimum@gmail.com>
 */
class ConfigureRolesEvent extends Event
{
    /**
     * @var string
     */
    public const CONFIGURE = 'integrated_roles.configure';

    /**
     * @var array
     */
    private $roles;

    /**
     * ConfigureRoleEvent constructor.
     *
     * @param array $roles
     */
    public function __construct($roles)
    {
        $this->roles = $roles;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     */
    public function addRoles($roles)
    {
        if (!$roles) {
            return;
        }

        foreach ($roles as $role) {
            $this->roles[$role] = $role;
        }
    }
}
