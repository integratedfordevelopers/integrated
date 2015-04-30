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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TinyMCEType extends AbstractType
{

    public function getParent()
    {
        return 'textarea';
    }

    public function getName()
    {
        return 'integrated_tinymce';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'freehtml' => false,
        ));

        $resolver->setAllowedTypes(array(
            'freehtml' => 'bool',
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['freehtml'] = $options['freehtml'];
    }
}