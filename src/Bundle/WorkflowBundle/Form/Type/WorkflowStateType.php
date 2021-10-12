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

use Doctrine\Persistence\ObjectRepository;
use Integrated\Bundle\WorkflowBundle\Entity\Definition;
use Integrated\Bundle\WorkflowBundle\Form\EventListener\WorkflowStateListener;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class WorkflowStateType extends AbstractType
{
    /**
     * @var ObjectRepository
     */
    private $repository;

    /**
     * @param ObjectRepository $repository
     */
    public function __construct(ObjectRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // The content of this form type is solely based on state that is not set yet. So
        // the only thing that is added is a listener that will update this type with more
        // fields based on the set state.
        //
        // The fields that can be added are "current" and "next". The "current" field holds
        // a read only text representation of the current state and the "next" field lets
        // the user select the next state to move to.

        $builder->addEventSubscriber(new WorkflowStateListener($options['workflow']));
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        if (!$form->has('current')) {
            return;
        }

        // The current field is just a text field but to give it the possibility to style is
        // differently a new block prefix will be added just before the last one. So one can
        // use the integrated_workflow_state_text_widget or workflow_state_text_widget block
        // to use a different template for this field.

        $child = $view->children['current'];

        $last = array_pop($child->vars['block_prefixes']);

        $child->vars['block_prefixes'][] = 'workflow_state_text';
        $child->vars['block_prefixes'][] = 'integrated_workflow_state_text';
        $child->vars['block_prefixes'][] = $last;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $workflowNormalizer = function (Options $options, $workflow) {
            if (\is_string($workflow)) {
                $workflow = $this->repository->find($workflow);
            }

            if (!$workflow instanceof Definition) {
                throw new InvalidOptionsException(sprintf(
                    'The option "%s" could not be normalized to a valid "%s" object',
                    'workflow',
                    'Integrated\\Bundle\\WorkflowBundle\\Entity\\Definition'
                ));
            }

            return $workflow;
        };

        $resolver->setRequired('workflow');
        $resolver->setAllowedTypes('workflow', ['string', 'Integrated\\Bundle\\WorkflowBundle\\Entity\\Definition']);

        $resolver->setNormalizer('workflow', $workflowNormalizer);

        $resolver->setDefault('empty_data', null);
        $resolver->setDefault('data_class', 'Integrated\\Bundle\\WorkflowBundle\\Entity\\Definition\\State');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_workflow_state';
    }
}
