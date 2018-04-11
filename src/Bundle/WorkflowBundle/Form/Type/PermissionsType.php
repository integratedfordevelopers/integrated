<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WorkflowBundle\Form\Type;

use Integrated\Bundle\WorkflowBundle\Form\DataTransformer\PermissionTransformer;
use Integrated\Common\Form\Type\PermissionsType as CommonPermissionsType;
use Doctrine\Common\Persistence\ObjectRepository;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class PermissionsType extends CommonPermissionsType
{
    /**
     * @return string
     */
    protected function getTransformer()
    {
        return new PermissionTransformer($this->repository);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_workflow_definition_permissions';
    }
}
