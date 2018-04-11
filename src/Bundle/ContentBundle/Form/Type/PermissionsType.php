<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Form\Type;

use Integrated\Common\Form\Type\PermissionsType as CommonPermissionsType;
use Integrated\Bundle\ContentBundle\Form\DataTransformer\PermissionTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PermissionsType extends CommonPermissionsType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'required' => false,
            'read-label' => 'Read access',
            'write-label' => 'Write access',
            'placeholder' => 'Everyone',
        ]);
    }

    /**
     * @return string
     */
    protected function getTransformer()
    {
        return new PermissionTransformer($this->repository);
    }
}
