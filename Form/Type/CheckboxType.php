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

use Symfony\Component\Form\Extension\Core\Type\CheckboxType as BaseCheckboxType;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class CheckboxType extends BaseCheckboxType
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        if ($options['align'] && !in_array('choice', $view->parent->vars['block_prefixes'])) {
            $view->vars['attr']['align_widget'] = true;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(['align' => true]);
        $resolver->setAllowedTypes(['align' => 'bool']);
    }
}
