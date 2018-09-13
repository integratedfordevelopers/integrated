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

use Doctrine\Common\Collections\ArrayCollection;
use Integrated\Bundle\WorkflowBundle\Entity\Definition\State;
use Integrated\Bundle\WorkflowBundle\Form\Model;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ExtractTransitionsFromCollectionListener implements EventSubscriberInterface
{
    /**
     * @var PropertyAccessorInterface
     */
    private $accessor;

    /**
     * Creates a new transition from collection extractor listener.
     *
     * @param PropertyAccessorInterface $accessor
     */
    public function __construct(PropertyAccessorInterface $accessor = null)
    {
        $this->accessor = $accessor ?: PropertyAccess::createPropertyAccessor();
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'onPrepare',
            FormEvents::PRE_SUBMIT => 'onPrepare',

            FormEvents::POST_SET_DATA => 'onSetData',
            FormEvents::POST_SUBMIT => 'onGetData',
        ];
    }

    /**
     * Add the transitions field to children of the collection.
     *
     * @param FormEvent $event
     */
    public function onPrepare(FormEvent $event)
    {
        $form = $event->getForm();

        if (!$form->count()) {
            return;
        }

        $data = $this->getChoices($event->getData());

        foreach ($form->all() as $child) {
            if ($child->has('transactions')) {
                $child->remove('transactions');
            }

            $child->add('transitions', ChoiceType::class, [
                'required' => false,

                // The transitions will be "manually" mapped because potential new States that are
                // created in the SUBMIT events will not be available, as a State object, until the
                // POST_SUBMIT event. So it is not possible to create a complete and correct list
                // of States in the execution of the PRE_* events to feed to a view transformer.

                'mapped' => false,

                'choices' => $this->getChoicesFiltered($data, $child->getName()),
                'choice_label' => 'label',
                'choice_value' => 'value',

                'multiple' => true,
                'expanded' => false,
            ]);
        }
    }

    /**
     * Set the view data based on the current state transitions.
     *
     * This will convert the transitions states to a list of numbers that represent the
     * index of the state in the collection. This will be done for all the children in
     * the collection.
     *
     * @param FormEvent $event
     */
    public function onSetData(FormEvent $event)
    {
        $form = $event->getForm();

        if (!$form->count()) {
            return;
        }

        // first build the index then set the data on the children. This can not be done in
        // one foreach run as the index need to be complete before converting to the view data.

        $index = [];

        foreach ($form->all() as $child) {
            $data = $child->getData();

            if ($data instanceof State) {
                $index[spl_object_hash($data)] = $child->getName();
            }
        }

        foreach ($form->all() as $child) {
            $data = $child->getData();

            if (!$data instanceof State) {
                continue;
            }

            if (!$child->has('transitions')) {
                continue;
            }

            // the index got the child keys where every State resides in the collection. So now we
            // convert the States in the transitions to the child index with in the collection. That
            // way we can also keep track of new States since those don't have a id yet.

            $selection = [];

            foreach ($data->getTransitions() as $data) {
                $hash = spl_object_hash($data);

                if (isset($index[$hash])) {
                    $selection[] = new Model\State($index[$hash], $data->getName());
                }
            }

            $child->get('transitions')->setData($selection);
        }
    }

    /**
     * Set the state transitions in based on the view data.
     *
     * This will convert the view data to a set of states to set as the transitions. This
     * will be done for all the children in the collection.
     *
     * @param FormEvent $event
     */
    public function onGetData(FormEvent $event)
    {
        $form = $event->getForm();

        if (!$form->count()) {
            return;
        }

        // Build a index with all the State data. This could also be done in one foreach
        // loop but to slim down on the method calls and instanceof check it is done ones
        // before converting the view data.

        $index = [];

        foreach ($form->all() as $child) {
            $data = $child->getData();

            if ($data instanceof State) {
                $index[$child->getName()] = $data;
            }
        }

        foreach ($form->all() as $child) {
            $data = $child->getData();

            if (!$data instanceof State) {
                continue;
            }

            if (!$child->has('transitions')) {
                continue;
            }

            $data->setTransitions(new ArrayCollection()); // clear the current transitions

            // The values in the view represent the index numbers of the State in de index, which
            // correspond directly to the index of the child in the collection.

            foreach ($child->get('transitions')->getData() as $value) {
                if (!$value instanceof Model\State) {
                    continue;
                }

                if (isset($index[$value->getValue()])) {
                    $data->addTransition($index[$value->getValue()]);
                }
            }
        }
    }

    /**
     * Get a array with choices based on the given data.
     *
     * The values of the choices are the same as the array keys from the data array and
     * the labels is the name field extracted from the data.
     *
     * @param array $data
     *
     * @return Model\State[]
     */
    protected function getChoices(array $data)
    {
        $choices = [];

        // The data could be a array of objects if its converted from the pre_set_data and
        // a array of scalars when its converted from the pre_submit. Also the name value
        // is not guaranteed to be present so a property accessor is used to be on the
        // safe side.

        foreach ($data as $index => $value) {
            $name = $this->accessor->getValue($value, \is_object($value) ? 'name' : '[name]');
            $name = trim($name);

            $choices[$index] = new Model\State($index, $name);
        }

        return $choices;
    }

    /**
     * Build a choice list based on the given choice but filter out the current state.
     *
     * @param array $choices
     * @param int   $current
     *
     * @return Model\State[]
     */
    protected function getChoicesFiltered(array $choices, $current)
    {
        if (isset($choices[$current])) {
            unset($choices[$current]);
        }

        return $choices;
    }
}
