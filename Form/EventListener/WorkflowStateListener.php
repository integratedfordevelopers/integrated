<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WorkflowBundle\Form\EventListener;

use Integrated\Bundle\WorkflowBundle\Entity\Definition;
use Integrated\Bundle\WorkflowBundle\Entity\Definition\State;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class WorkflowStateListener implements EventSubscriberInterface
{
    /**
     * @var Definition
     */
    private $workflow;

    /**
     * @param Definition $workflow
     */
    public function __construct(Definition $workflow)
    {
        $this->workflow = $workflow;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA  => [['onPrepareData', 10], ['onPrepareForm']],
            FormEvents::POST_SET_DATA => 'onPostData',
            FormEvents::SUBMIT        => 'onSubmit'
        ];
    }

    /**
     * Validate the state
     *
     * @param FormEvent $event
     */
    public function onPrepareData(FormEvent $event)
    {
        $event->setData($this->getState($event));
    }

    /**
     * Add extra field based on the state
     *
     * @param FormEvent $event
     */
    public function onPrepareForm(FormEvent $event)
    {
        $form = $event->getForm();

        foreach ($form->all() as $child) {
            $form->remove($child->getName()); // remove all the children
        }

        $data = $event->getData();

        if (!$data instanceof State) {
            return; // no valid state found
        }

        $form->add('current', 'text', [
            'read_only' => true,
            'mapped' => false,
            'data' => $data->getName(),
            'label' => 'Workflow status'
        ]);

        $choices = $this->getChoices($data);

        if (!$choices) {
            return; // seams there are no next states
        }

        $form->add('next', 'choice', [
            'label' => 'Next status',

            'choices' => $choices,
            'choices_as_values' => true,
            'choice_value' => 'id',
            'choice_label' => 'name',

            'placeholder' => 'Don\'t change',

            'expanded' => true,
            'mapped' => false,
            'empty_data' => $data,
        ]);
    }

    /**
     * Force the placeholder to be selected
     *
     * @param FormEvent $event
     */
    public function onPostData(FormEvent $event)
    {
        $form = $event->getForm();

        if (!$form->has('next')) {
            return;
        }

        $form = $form->get('next');

        if (!$form->has('placeholder')) {
            return;
        }

        $form->get('placeholder')->setData(true);
    }

    /**
     * Set the state
     *
     * @param FormEvent $event
     */
    public function onSubmit(FormEvent $event)
    {
        if ($state = $this->getData($event)) {
            $event->setData($state);
        }
    }

    /**
     * Get the current state from the data
     *
     * @param FormEvent $event
     * @return null | State
     */
    protected function getState(FormEvent $event)
    {
        $data = $event->getData();

        if ($data instanceof State && $this->workflow->hasState($data)) {
            return $data;
        }

        // Data is not a state or is not a state in the given workflow. So fail silently and pick
        // the default state instead.

        return $this->workflow->getDefault();
    }

    /**
     * Get the next state from the form
     *
     * @param FormEvent $event
     * @return null | State
     */
    protected function getData(FormEvent $event)
    {
        $form = $event->getForm();

        if (!$form->has('next')) {
            return null;
        }

        $form = $form->get('next');

        if (!$data = $form->getData()) {
            $data = $form->getConfig()->getEmptyData();
        }

        return $data;
    }

    /**
     * Get a array of the transitions for the given state.
     *
     * @param State $state
     * @return array
     */
    protected function getChoices(State $state)
    {
        $choices = [];

        foreach ($state->getTransitions() as $transition) {
            if ($state === $transition) {
                // This should not happen as the transitions should not contain the current state
                // it self but it is possible so check for it anyways

                continue;
            }

            $choices[] = $transition;
        }

        return $choices;
    }
}
