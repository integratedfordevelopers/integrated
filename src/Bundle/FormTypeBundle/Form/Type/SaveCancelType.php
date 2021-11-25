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

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class SaveCancelType extends SubmitType
{
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $view->vars['type'] = 'submit';
        $view->vars['cancel_route'] = $options['cancel_route'];
        $view->vars['cancel_route_parameters'] = $options['cancel_route_parameters'];
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'cancel_route_parameters' => [],
            'label' => 'Save',
            'button_class' => 'orange',
        ]);

        $resolver->setRequired('cancel_route');

        $resolver->setAllowedTypes('cancel_route', ['string']);
        $resolver->setAllowedTypes('cancel_route_parameters', ['array']);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_save_cancel';
    }
}
