<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\FormTypeBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 * @deprecated
 */
class TinyMCEType extends EditorType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        @trigger_error(
            sprintf(
                'The formtype %s is deprecated in favour of %s and will be removed in the future',
                self::getName(),
                parent::getName()
            ),
            E_USER_DEPRECATED
        );

        parent::setDefaultOptions($resolver);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return TextareaType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_tinymce';
    }
}
