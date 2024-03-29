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

interface PermissionInterface
{
    public const READ = 1;
    public const WRITE = 2;

    /**
     * @return string
     */
    public function getGroup();

    /**
     * @return int
     */
    public function getMask();

    /**
     * @param int $mask
     *
     * @return bool
     */
    public function hasMask($mask);
}
