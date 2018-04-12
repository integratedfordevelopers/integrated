<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Form\DataTransformer;

use Integrated\Bundle\ContentBundle\Document\Permission\Embedded\Permission;
use Integrated\Common\Form\DataTransformer\PermissionTransformer as BasePermissionTransformer;

class PermissionTransformer extends BasePermissionTransformer
{
    /**
     * @return string
     */
    protected function getPermissionClass()
    {
        return Permission::class;
    }
}
