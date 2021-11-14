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

use Integrated\Bundle\FormTypeBundle\Form\Type\BootstrapCollectionType;
use Integrated\Bundle\WorkflowBundle\Entity\Definition;
use Integrated\Bundle\WorkflowBundle\Form\EventListener\ExtractDefaultStateFromCollectionListener;
use Integrated\Bundle\WorkflowBundle\Form\EventListener\ExtractTransitionsFromCollectionListener;
use Integrated\Common\Validator\Constraints\UniqueEntry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class DefinitionFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, [
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 3]),
            ],
        ]);

        $builder->add('states', BootstrapCollectionType::class, [
            'label' => 'Statuses',
            'entry_type' => StateType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'entry_options' => ['transitions' => 'empty'],
            'constraints' => [
                new Count(['min' => 1]),
                new UniqueEntry(['fields' => ['name'], 'caseInsensitive' => true]),
            ],
        ]);

        // Transitions are actually part of the "workflow_definition_state" but they are based
        // on states in the collection. The problem is that only the last state in the collection
        // will have full access to the all the states in the collection in its listener. Now
        // you could try to work around this by using the POST_* events but POST_SUBMIT will not
        // allow you to modify the form anymore. So to make this work the collection it self
        // will manager the transitions field for the "workflow_definition_state" form type as
        // the collection will have access to all the required state data in its PRE_* events.

        $builder->get('states')->addEventSubscriber(new ExtractTransitionsFromCollectionListener());

        // Add eventSubscriber which extracts the default State from the State Collection
        $builder->addEventSubscriber(new ExtractDefaultStateFromCollectionListener());
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $child = $view->children['states'];

        $last = array_pop($child->vars['block_prefixes']);

        // add some extra names to block_prefixes to allow for more templating options

        $child->vars['block_prefixes'][] = 'workflow_definition_state_collection';
        $child->vars['block_prefixes'][] = 'integrated_workflow_definition_state_collection';
        $child->vars['block_prefixes'][] = $last;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $emptyData = function (FormInterface $form) {
            return new Definition();
        };

        $resolver->setDefault('empty_data', $emptyData);
        $resolver->setDefault('data_class', 'Integrated\\Bundle\\WorkflowBundle\\Entity\\Definition');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_workflow_definition';
    }
}
