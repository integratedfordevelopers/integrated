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

use Symfony\Component\OptionsResolver\OptionsResolver;
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
        if (in_array('type', $options['fields'])) {
            $builder->add('type', 'choice', [
                'placeholder' => '',
                'required'    => false,
                'choices'     => [
                    'postal'   => 'Postal address',
                    'visiting' => 'Visiting address',
                    'mailing'  => 'Mailing address',
                ],
            ]);
        }

        if (in_array('name', $options['fields'])) {
            $builder->add('name', 'text', [
                'required' => false,
            ]);
        }

        if (in_array('address1', $options['fields'])) {
            $builder->add('address1', 'text', [
                'label'    => 'Address line 1',
                'required' => false,
            ]);
        }

        if (in_array('address2', $options['fields'])) {
            $builder->add('address2', 'text', [
                'label'    => 'Address line 2',
                'required' => false,
            ]);
        }

        if (in_array('zipcode', $options['fields'])) {
            $builder->add('zipcode', 'text', [
                'required' => false,
            ]);
        }

        if (in_array('city', $options['fields'])) {
            $builder->add('city', 'text', [
                'required' => false,
            ]);
        }

        if (in_array('country', $options['fields'])) {
            $builder->add('country', 'country', [
                'placeholder' => '',
                'required'    => false,
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Integrated\\Bundle\\ContentBundle\\Document\\Content\\Embedded\\Address',
            'fields'     => ['type', 'name', 'country', 'address1', 'address2', 'zipcode', 'city'], // @todo validate options (INTEGRATED-627)
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_address';
    }
}
