<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WorkflowBundle\Form\DataTransformer;

use Integrated\Bundle\WorkflowBundle\Entity\Definition\Permission;
use Integrated\Common\Form\DataTransformer\PermissionTransformer as CommonPermissionTransformer;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class PermissionTransformer extends CommonPermissionTransformer
{
    /**
     * @return string
     */
    protected function getPermissionClass()
    {
        return Permission::class;
    }
}
