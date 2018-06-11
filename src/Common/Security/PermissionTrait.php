<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Security;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

trait PermissionTrait
{
    /**
     * @var ArrayCollection
     */
    protected $permissions;

    /**
     * @return ArrayCollection
     */
    public function getPermissions()
    {
        if (!$this->permissions instanceof Collection) {
            $this->permissions = new ArrayCollection();
        }

        return $this->permissions;
    }

    /**
     * @param Collection $permissions
     *
     * @return $this
     */
    public function setPermission(Collection $permissions)
    {
        $this->permissions = $permissions;

        return $this;
    }

    /**
     * @param Permission $permission
     *
     * @return $this
     */
    public function addPermission(Permission $permission)
    {
        /** @var Permission $exist */
        if ($exist = $this->getPermission($permission->getGroup())) {
            $exist->setMask($permission->getMask());
        } else {
            $this->getPermissions()->add($permission);
        }

        return $this;
    }

    /**
     * @param Permission $permission
     *
     * @return $this
     */
    public function removePermission(Permission $permission)
    {
        $this->getPermissions()->removeElement($permission);

        return $this;
    }

    /**
     * @param int $groupId
     *
     * @return Permission
     */
    public function getPermission($groupId)
    {
        return $this->getPermissions()->filter(function ($permission) use ($groupId) {
            if ($permission instanceof Permission) {
                if ($permission->getGroup() == $groupId) {
                    return true;
                }
            }

            return false;
        })->first();
    }
}
