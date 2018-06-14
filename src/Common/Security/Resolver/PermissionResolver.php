<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Security\Resolver;

use Integrated\Bundle\UserBundle\Model\GroupableInterface;
use Integrated\Common\Security\PermissionInterface;

class PermissionResolver
{
    /**
     * @param GroupableInterface    $user
     * @param PermissionInterface[] $permissions
     *
     * @return array
     */
    public static function getPermissions(GroupableInterface $user, $permissions = []): array
    {
        $groups = [];

        foreach ($user->getGroups() as $group) {
            $groups[$group->getId()] = $group->getId();
        }

        $mask = 0;
        $hasReadPermissions = false;
        $hasWritePermissions = false;

        if ($groups) {
            foreach ($permissions as $permission) {
                if (PermissionInterface::READ === ($permission->getMask() & PermissionInterface::READ)) {
                    $hasReadPermissions = true;
                }

                if (PermissionInterface::WRITE === ($permission->getMask() & PermissionInterface::WRITE)) {
                    $hasWritePermissions = true;
                }

                if (isset($groups[$permission->getGroup()])) {
                    $mask |= $permission->getMask();
                }
            }
        }

        return [
            'read' => $hasReadPermissions ? (PermissionInterface::READ === ($mask & PermissionInterface::READ)) : true,
            'write' => $hasWritePermissions ? (PermissionInterface::WRITE === ($mask & PermissionInterface::WRITE)) : true,
        ];
    }
}
