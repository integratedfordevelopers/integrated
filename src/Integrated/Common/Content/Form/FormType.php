<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content\Form;

use Integrated\Common\Content\Form\Event\BuilderEvent;
use Integrated\Common\Content\Form\Event\FieldEvent;
use Integrated\Common\Content\Form\Event\OptionsEvent;
use Integrated\Common\Content\Form\Event\ViewEvent;

use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\Form\Mapping\MetadataInterface;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class FormType extends AbstractType implements FormTypeInterface
{
    /**
     * @var ContentTypeInterface
     */
    protected $contentType;

    /**
     * @var MetadataInterface
     */
    protected $metadata;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher = null;

    /**
     * @var string
     */
    private $name = null;

    /**
     * @param ContentTypeInterface $contentType
     * @param MetadataInterface $metadata
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(ContentTypeInterface $contentType, MetadataInterface $metadata, EventDispatcherInterface $dispatcher = null)
    {
        $this->contentType = $contentType;
        $this->metadata = $metadata;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $dispatcher = $this->getEventDispatcher();

        // Allow events to change the options or add fields at the start of the form
        if ($dispatcher->hasListeners(Events::PRE_BUILD)) {
            $event = new BuilderEvent($this->contentType, $this->metadata, $builder);
            $event->setOptions($options);

            $dispatcher->dispatch(Events::PRE_BUILD, $event);

            $options = $event->getOptions();
        }

        foreach ($this->metadata->getFields() as $field) {

            // Allow events to add fields before the supplied field
            if ($dispatcher->hasListeners(Events::PRE_BUILD_FIELD)) {
                $event = new BuilderEvent($this->contentType, $this->metadata, $builder, $field->getName());
                $event->setOptions($options);

                $dispatcher->dispatch(Events::PRE_BUILD_FIELD, $event);
            }

            if ($this->contentType->hasField($field->getName())) {
                $config = $this->contentType->getField($field->getName());

                // Allow events to change the supplied field options or even remove it from the form
                if ($dispatcher->hasListeners(Events::BUILD_FIELD)) {
                    $event = new FieldEvent($this->contentType, $this->metadata);
                    $event->setOptions($options);
                    $event->setField(clone $config); // don't allow the original to be changed.

                    $dispatcher->dispatch(Events::BUILD_FIELD, $event);

                    $config = $event->isIgnored() ? null : $event->getField();
                }

                if ($config) {
                    // The config could be changed but even though it possible don't accept a new field
                    // name. The correct way to change the field name is to ignore this field and add a
                    // new field before or after this field by using the PRE or POST_BUILD_FIELD event.

                    $builder->add($field->getName(), $config->getType(), $config->getOptions());
                }
            }

            // Allow events to add fields after the supplied field
            if ($dispatcher->hasListeners(Events::POST_BUILD_FIELD)) {
                $event = new BuilderEvent($this->contentType, $this->metadata, $builder, $field->getName());
                $event->setOptions($options);

                $dispatcher->dispatch(Events::POST_BUILD_FIELD, $event);
            }
        }

        // Allow events to add fields at the end of the form
        if ($dispatcher->hasListeners(Events::POST_BUILD)) {
            $event = new BuilderEvent($this->contentType, $this->metadata, $builder);
            $event->setOptions($options);

            $dispatcher->dispatch(Events::POST_BUILD, $event);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $dispatcher = $this->getEventDispatcher();

        if ($dispatcher->hasListeners(Events::PRE_VIEW)) {
            $event = new ViewEvent($this->contentType, $this->metadata, $view, $form);
            $event->setOptions($options);

            $this->getEventDispatcher()->dispatch(Events::PRE_VIEW, $event);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $dispatcher = $this->getEventDispatcher();

        if ($dispatcher->hasListeners(Events::POST_VIEW)) {
            $event = new ViewEvent($this->contentType, $this->metadata, $view, $form);
            $event->setOptions($options);

            $this->getEventDispatcher()->dispatch(Events::POST_VIEW, $event);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $dispatcher = $this->getEventDispatcher();

        if ($dispatcher->hasListeners(Events::PRE_OPTIONS)) {
            $dispatcher->dispatch(Events::PRE_OPTIONS, new OptionsEvent($this->contentType, $this->metadata, $resolver));
        }

        $resolver->setDefaults([
            'data_class' => $this->contentType->getClass(),
            'empty_data' => function(FormInterface $form) { return $this->contentType->create(); },
        ]);

        if ($dispatcher->hasListeners(Events::POST_OPTIONS)) {
            $dispatcher->dispatch(Events::POST_OPTIONS, new OptionsEvent($this->contentType, $this->metadata, $resolver));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->contentType;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'form';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        if (null === $this->name) {
            $this->name = preg_replace('#[^a-zA-Z0-9\-_]#', '', 'integrated_content_form_' . $this->contentType->getType());
            $this->name = strtolower($this->name);
        }

        return $this->name;
    }

    /**
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        if (null == $this->dispatcher) {
            $this->dispatcher = new EventDispatcher();
        }

        return $this->dispatcher;
    }
}
