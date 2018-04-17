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
use Integrated\Common\Security\Permission;

class PermissionResolver
{
    /**
     * @param GroupableInterface $user
     * @param Permission[] $permissions
     * @return array
     */
    public static function getPermissions(GroupableInterface $user, $permissions = []): array
    {
        $groups = [];

        foreach ($user->getGroups() as $group) {
            $groups[$group->getId()] = $group->getId(); // create lookup table
        }

        $mask = 0;

        if ($groups) {
            $maskAll = Permission::READ | Permission::WRITE;

            foreach ($permissions as $permission) {
                if (isset($groups[$permission->getGroup()])) {
                    $mask = $mask | ($maskAll & $permission->getMask());

                    if ($mask == $maskAll) {
                        break;
                    }
                }
            }
        }

        return [
            'read' => (bool) ($mask & Permission::READ),
            'write' => (bool) ($mask & Permission::WRITE),
        ];
    }
}
