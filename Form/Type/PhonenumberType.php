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
class PhonenumberType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_phonenumber';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('type', 'choice', array(
            'choices' => [
                'mobile' => 'Mobile',
                'home' => 'Home'
            ]
        ));

        $builder->add('number', 'text', array(
            'label' => 'Phone number',
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
            'data_class' => 'Integrated\\Bundle\\ContentBundle\\Document\\Content\\Embedded\\Phonenumber'
        ));
    }
}