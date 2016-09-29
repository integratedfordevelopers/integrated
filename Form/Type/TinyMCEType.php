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

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 * @deprecated
 */
class TinyMCEType extends EditorType
{
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
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
        return 'textarea';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_tinymce';
    }
}
