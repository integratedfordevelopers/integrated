<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Form\EventListener;

use Integrated\Bundle\ContentBundle\Document\FormConfig\Embedded\Field\CustomField;
use Integrated\Bundle\ContentBundle\Document\FormConfig\Embedded\Field\DocumentField;
use Integrated\Bundle\ContentBundle\Document\FormConfig\Embedded\Field\RelationField;
use Integrated\Bundle\ContentBundle\Form\Type\FormConfigFieldType;
use Integrated\Common\FormConfig\FormConfigFieldInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class FormConfigFieldsListener implements EventSubscriberInterface
{
    /**
     * @var DocumentField[]
     */
    private $documents;

    /**
     * @var RelationField[]
     */
    private $relations;

    /**
     * @param FormConfigFieldInterface[] $fields
     */
    public function __construct(array $fields)
    {
        foreach ($fields as $field) {
            if ($field instanceof DocumentField) {
                $this->documents[$field->getName()] = $field;
            } elseif ($field instanceof RelationField) {
                $this->relations[$field->getName()] = $field;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => [['cleanData', 20], ['updateSelected', 10]],
            FormEvents::POST_SET_DATA => 'updateAvailable',
            FormEvents::PRE_SUBMIT => 'updateSelectedAfterSubmit',
            FormEvents::SUBMIT => [['sliceData', 20], ['updateAvailable', 10]],
        ];
    }

    /**
     * Remove none existing document and relation field from the data set.
     *
     * @param FormEvent $event
     */
    public function cleanData(FormEvent $event)
    {
        $fields = $event->getData();

        if ($fields === null) {
            $fields = [];
        }

        if (!is_iterable($fields)) {
            throw new UnexpectedTypeException($fields, 'iterable');
        }

        $cleaned = [];

        foreach ($fields as $field) {
            if ($field instanceof DocumentField) {
                if ($this->documents[$field->getName()] ?? null) {
                    $cleaned[] = $field;
                }
            } elseif ($field instanceof RelationField) {
                if ($this->relations[$field->getName()] ?? null) {
                    $cleaned[] = $field;
                }
            } elseif ($field instanceof CustomField) {
                $cleaned[] = $field;
            }
        }

        $event->setData($cleaned);
    }

    /**
     * Create a list of all the selected fields.
     *
     * All the field will get a unique id as name so that no id conflict will arise
     * between the selected and a available fields.
     *
     * @param FormEvent $event
     */
    public function updateSelected(FormEvent $event)
    {
        $form = $event->getForm();

        foreach ($form as $name => $child) {
            $form->remove($name);
        }

        $options = [
            'content_type' => $form->getConfig()->getOption('content_type')
        ];

        foreach (array_keys($event->getData()) as $index) {
            $form->add(uniqid('', false), FormConfigFieldType::class, ['property_path' => '['.$index.']'] + $options);
        }
    }

    /**
     * Create a list of all the selected fields.
     *
     * This is almost the same as @see updateSelected the only differnce is that the
     * submitted field names will be used.
     *
     * @param FormEvent $event
     */
    public function updateSelectedAfterSubmit(FormEvent $event)
    {
        $form = $event->getForm();

        foreach ($form as $name => $child) {
            $form->remove($name);
        }

        $options = [
            'content_type' => $form->getConfig()->getOption('content_type')
        ];

        $counter = 0;

        foreach (array_keys($event->getData()) as $index) {
            $form->add($index, FormConfigFieldType::class, ['property_path' => '['.$counter++.']'] + $options);
        }
    }

    /**
     * The data mapper only adds and does not remove, so this will slice the
     * data set to correct size.
     *
     * @param FormEvent $event
     */
    public function sliceData(FormEvent $event)
    {
        $event->setData(array_slice($event->getData(), 0, count($event->getForm())));
    }

    /**
     * Create a list of all the document and relation field that are currently not selected.
     *
     * @param FormEvent $event
     */
    public function updateAvailable(FormEvent $event)
    {
        $available = [
            DocumentField::class => $this->documents,
            RelationField::class => $this->relations,
        ];

        foreach ($event->getData() as $field) {
            $class = get_class($field);

            if (isset($available[$class])) {
                unset($available[$class][$field->getName()]);
            }
        }

        $available = array_values(array_map('array_values', $available));
        $available = array_merge(...$available);

        usort($available, function (FormConfigFieldInterface $a, FormConfigFieldInterface $b) {
            return strtolower($a->getName()) <=> strtolower($b->getName());
        });

        $form = $event->getForm()->getConfig()->getAttribute('available');

        foreach ($form as $name => $child) {
            $form->remove($name);
        }

        $options = [
            'content_type' => $event->getForm()->getConfig()->getOption('content_type')
        ];

        foreach (array_keys($available) as $index) {
            $form->add(uniqid('', false), FormConfigFieldType::class, ['property_path' => '['.$index.']'] + $options);
        }

        $form->setData($available);
    }
}
