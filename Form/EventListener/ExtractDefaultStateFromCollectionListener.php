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

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ExtractDefaultStateFromCollectionListener implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SUBMIT => 'onPreSubmit',
            FormEvents::POST_SET_DATA => 'onPostSetData',
            FormEvents::SUBMIT => 'onSubmit'
        ];
    }

    /**
     * Remove the default value of the Definition
     *
     * @param FormEvent $event
     */
    public function onPreSubmit(FormEvent $event)
    {
        $definition = $event->getForm()->getData();

        if ($definition instanceof Definition) {
            $definition->setDefault();
        }
    }

    /**
     * Mark the default State as default
     *
     * @param FormEvent $event
     */
    public function onPostSetData(FormEvent $event)
    {
        $form = $event->getForm();

        if (!$form->has('states')) {
            return;
        }

        $states = $form->get('states');
        foreach ($states->all() as $child) {

            $state = $child->getData();
            if (!$state instanceof State) {
                continue;
            }

            if ($child->has('default')) {
                $child->get('default')->setData($state->isDefault());
            }
        }
    }

    /**
     * Set the default State of a Definition
     *
     * @param FormEvent $event
     */
    public function onSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $definition = $event->getData();

        if (!$definition instanceof Definition) {
            return;
        }

        if (!$form->has('states')) {
            return;
        }

        $states = $form->get('states');
        foreach ($states->all() as $child) {

            $state = $child->getData();
            if (!$state instanceof State) {
                continue;
            }

            if (!$child->has('default')) {
                continue;
            }

            if (true === $child->get('default')->getData()) {
                $definition->setDefault($state);
            }
        }
    }
}
