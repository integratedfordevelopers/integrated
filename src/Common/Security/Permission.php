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

use Integrated\Bundle\UserBundle\Model\GroupInterface;

class Permission implements PermissionInterface
{
    /**
     * @var string
     */
    protected $group;

    /**
     * @var int
     */
    protected $mask;

    /**
     * @param string|GroupInterface $group
     *
     * @return $this
     */
    public function setGroup($group)
    {
        if ($group instanceof GroupInterface) {
            $group = $group->getId();
        }

        $this->group = (string) $group;

        return $this;
    }

    /**
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param int $mask
     *
     * @return $this
     */
    public function setMask($mask)
    {
        $this->mask = (int) $mask;

        return $this;
    }

    /**
     * @return int
     */
    public function getMask()
    {
        return $this->mask;
    }

    /**
     * @param int $mask
     *
     * @return $this
     */
    public function addMask($mask)
    {
        $this->mask = $this->mask | (int) $mask;

        return $this;
    }

    /**
     * @param int $mask
     *
     * @return $this
     */
    public function removeMask($mask)
    {
        $this->mask = $this->mask - ($this->mask & (int) $mask);

        return $this;
    }

    /**
     * @param int $mask
     *
     * @return bool
     */
    public function hasMask($mask)
    {
        return (bool) ($this->mask & $mask) == $mask;
    }
}
