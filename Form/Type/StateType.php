<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WorkflowBundle\Form\Type;

use Integrated\Bundle\WorkflowBundle\Entity\Definition\State;
use Integrated\Bundle\WorkflowBundle\Form\EventListener\ExtractTransitionsFromDataListener;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class StateType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', [
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 3])
            ],
            'attr' => [
                'class' => 'state_name_input_field'
            ]
        ]);

        $builder->add('publishable', 'checkbox', ['required' => false]);
        $builder->add('default', 'checkbox', ['required' => false, 'mapped' => false]);
        $builder->add('permissions', 'workflow_definition_permissions', ['required' => false]);

        if ($options['transitions'] == 'data') {
            $builder->addEventSubscriber(new ExtractTransitionsFromDataListener());
        }

        if ($options['transitions'] == 'empty') {
            $builder->add('transitions', 'choice', [
                'required' => false,
                'mapped'   => false,

                'choices'  => [],

                'multiple' => true,
                'expanded' => false,
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $emptyData = function (FormInterface $form) {
            return new State();
        };

        $resolver->setDefault('empty_data', $emptyData);
        $resolver->setDefault('data_class', 'Integrated\\Bundle\\WorkflowBundle\\Entity\\Definition\\State');
        $resolver->setDefault('transitions', 'data');

        $resolver->setAllowedValues('transitions', ['data', 'empty', 'none']);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_workflow_definition_state';
    }
}
