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
        $builder->add('type', 'choice', array(
            'placeholder' => '',
            'choices'     => array(
                'postal'   => 'Postal address',
                'visiting' => 'Visiting address',
                'mailing'  => 'Mailing address',
            )
        ));

        $builder->add('country', 'country', array(
            'placeholder' => '',
        ));

        $builder->add('address1', 'text', array(
            'label' => 'Address line 1',
        ));

        $builder->add('address2', 'text', array(
            'label' => 'Address line 2',
        ));

        $builder->add('zipcode', 'text');

        $builder->add('city', 'text');
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
