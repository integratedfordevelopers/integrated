<?php

namespace Integrated\Bundle\FormTypeBundle\Form;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class AddressType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'address';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('type', 'choice', array(
            'choices' => [
                '0' => 'Postal address',
                '1' => 'Visiting address',
                '2' => 'Mailing address'
            ]
        ));

        $builder->add('country', 'country', array(
            'constraints' => [
                new NotBlank(),
            ]
        ));

        $builder->add('address1', 'text', array(
            'label' => 'Street',
            'constraints' => [
                new NotBlank(),
            ]
        ));

        $builder->add('zipcode', 'text', array(
            'constraints' => [
                new NotBlank(),
            ]
        ));

        $builder->add('city', 'text', [
            'constraints' => [
                new NotBlank(),
            ]
        ]);
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
