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
     * @param GroupableInterface $user
     * @param PermissionInterface[] $permissions
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
            foreach ($permissions as $permission) {
                if (isset($groups[$permission->getGroup()])) {
                    $mask |= $permission->getMask();
                }
            }
        }

        return [
            'read' => ($mask & PermissionInterface::READ) === PermissionInterface::READ,
            'write' => ($mask & PermissionInterface::WRITE) === PermissionInterface::WRITE,
        ];
    }
}
