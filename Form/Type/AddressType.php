<?php

namespace Integrated\Bundle\ContentBundle\Form\Type;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author Jurre de Jongh <jurre@e-active.nl>
 */
class AddressType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_address';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('type', 'choice', array(
            'choices' => array(
                'postal' => 'Postal address',
                'visiting' => 'Visiting address',
                'mailing' => 'Mailing address',
            )
        ));

        $builder->add('country', 'country', array(
            'constraints' => array(
                new NotBlank(),
            )
        ));

        $builder->add('address1', 'text', array(
            'label' => 'Address line 1',
            'constraints' => array(
                new NotBlank(),
            )
        ));

        $builder->add('address2', 'text', array(
            'label' => 'Address line 2',
            'constraints' => array(
                new NotBlank(),
            )
        ));

        $builder->add('zipcode', 'text', array(
            'constraints' => array(
                new NotBlank(),
            )
        ));

        $builder->add('city', 'text', array(
            'constraints' => array(
                new NotBlank(),
            )
        ));
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
}
