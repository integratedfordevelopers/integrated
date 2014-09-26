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

use Integrated\Bundle\WorkflowBundle\Entity\Definition;
use Integrated\Bundle\WorkflowBundle\Form\EventListener\ExtractTransitionsFromCollectionListener;

use Integrated\Common\Validator\Constraints\UniqueEntry;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class DefinitionFormType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', [
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 3])
            ]
        ]);

        $builder->add('states', 'bootstrap_collection', [
            'type'         => 'workflow_definition_state',
            'allow_add'    => true,
            'allow_delete' => true,
            'options'      => ['transitions' => 'empty'],
            'constraints'  => [
                new Count(['min' => 1]),
                new UniqueEntry(['fields' => ['name'], 'caseInsensitive' => true]),
            ]
        ]);

        // Transitions are actually part of the "workflow_definition_state" but they are based
        // on states in the collection. The problem is that only the last state in the collection
        // will have full access to the all the states in the collection in its listener. Now
        // you could try to work around this by using the POST_* events but POST_SUBMIT will not
        // allow you to modify the form anymore. So to make this work the collection it self
        // will manager the transitions field for the "workflow_definition_state" form type as
        // the collection will have access to all the required state data in its PRE_* events.

        $builder->get('states')->addEventSubscriber(new ExtractTransitionsFromCollectionListener());
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'empty_data' => function (FormInterface $form) {
                return new Definition();
            },
            'data_class' => 'Integrated\\Bundle\\WorkflowBundle\\Entity\\Definition',

            'constraints' => new UniqueEntity(['name']),
        ));
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'integrated_workflow_definition';
    }
}
