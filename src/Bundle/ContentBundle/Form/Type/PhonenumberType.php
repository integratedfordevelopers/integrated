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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author Jurre de Jongh <jurre@e-active.nl>
 */
class PhonenumberType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (\in_array('type', $options['fields'])) {
            $builder->add('type', ChoiceType::class, [
                'label' => $options['label_type'],
                'choices' => [
                    'Mobile' => 'mobile',
                    'Work' => 'work',
                    'Home' => 'home',
                ],
            ]);
        }

        if (\in_array('number', $options['fields'])) {
            $builder->add('number', TextType::class, [
                'label' => $options['label_number'],
                'constraints' => [
                    new NotBlank(),
                ],
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Integrated\\Bundle\\ContentBundle\\Document\\Content\\Embedded\\Phonenumber',
            'fields' => ['type', 'number'], // @todo validate options (INTEGRATED-627)
            'label_type' => 'Type',
            'label_number' => 'Phone number',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_phonenumber';
    }
}
