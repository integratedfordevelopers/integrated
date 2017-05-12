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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
class SelectionFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('selection', ChoiceType::class, [
            'label' => false,
            'choices' => $options['content'],
            'choices_as_values' => true,
            'choice_label' => function ($value, $key, $index) {
                return $key;
            },
            'multiple' => true,
            'expanded' => true,
            'error_bubbling' => true,
            'data' => $options['content'],
            'required' => true,
            'constraints' => [
                new NotBlank([
                    'message' => 'A selection is required to go further.',
                ]),
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('content');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'intgrated_content_bulk_select';
    }
}
