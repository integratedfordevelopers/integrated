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

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Jurre de Jongh <jurre@e-active.nl>
 */
class AddressType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('type', 'choice', [
            'placeholder' => '',
            'choices'     => [
                'postal'   => 'Postal address',
                'visiting' => 'Visiting address',
                'mailing'  => 'Mailing address',
            ],
            'required' => false,
        ]);

        $builder->add('name', 'text', ['required' => false]);

        $builder->add('country', 'country', ['placeholder' => '', 'required' => false]);

        $builder->add('address1', 'text', ['label' => 'Address line 1', 'required' => false]);

        $builder->add('address2', 'text', ['label' => 'Address line 2', 'required' => false]);

        $builder->add('zipcode', 'text', ['required' => false]);

        $builder->add('city', 'text', ['required' => false]);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Integrated\\Bundle\\ContentBundle\\Document\\Content\\Embedded\\Address'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_address';
    }
}
