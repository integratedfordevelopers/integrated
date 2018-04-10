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

use Integrated\Bundle\ContentBundle\Document\Channel\Embedded\Permission;
use Integrated\Common\Content\Permission as CommonPermission;
use Integrated\Common\Form\DataTransformer\PermissionTransformer;

class ChannelPermissionTransformer extends PermissionTransformer
{
    /**
     * @return string | CommonPermission
     */
    protected function getPermissionClass()
    {
        return Permission::class;
    }
}
