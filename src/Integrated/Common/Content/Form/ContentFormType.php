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
use Integrated\Common\Content\Form\Event\ViewEvent;

use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\ContentType\ResolverInterface;
use Integrated\Common\Form\Mapping\MetadataFactoryInterface;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
class ContentFormType extends AbstractType
{
    /**
     * @var MetadataFactoryInterface
     */
    protected $metadataFactory;

    /**
     * @var ResolverInterface
     */
    protected $resolver;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher = null;

    /**
     * @param MetadataFactoryInterface $metadataFactory
     * @param ResolverInterface $resolver
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(
        MetadataFactoryInterface $metadataFactory,
        ResolverInterface $resolver,
        EventDispatcherInterface $dispatcher = null
    ) {
        $this->metadataFactory = $metadataFactory;
        $this->resolver = $resolver;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $dispatcher = $this->getEventDispatcher();

        /** @var ContentTypeInterface $contentType */
        $contentType = $options['content_type'];

        $metadata = $this->metadataFactory->getMetadata($contentType->getClass());

        // Allow events to change the options or add fields at the start of the form
        if ($dispatcher->hasListeners(Events::PRE_BUILD)) {
            $event = new BuilderEvent($contentType, $metadata, $builder);
            $event->setOptions($options);

            $dispatcher->dispatch(Events::PRE_BUILD, $event);

            $options = $event->getOptions();
        }

        foreach ($metadata->getFields() as $field) {
            // Allow events to add fields before the supplied field
            if ($dispatcher->hasListeners(Events::PRE_BUILD_FIELD)) {
                $event = new BuilderEvent($contentType, $metadata, $builder, $field->getName());
                $event->setOptions($options);

                $dispatcher->dispatch(Events::PRE_BUILD_FIELD, $event);
            }

            if ($contentType->hasField($field->getName())) {
                $config = $contentType->getField($field->getName());

                // Allow events to change the supplied field options or even remove it from the form
                if ($dispatcher->hasListeners(Events::BUILD_FIELD)) {
                    $event = new FieldEvent($contentType, $metadata);
                    $event->setOptions($options);
                    $event->setData($builder->getData());
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
                $event = new BuilderEvent($contentType, $metadata, $builder, $field->getName());
                $event->setOptions($options);

                $dispatcher->dispatch(Events::POST_BUILD_FIELD, $event);
            }
        }

        // Allow events to add fields at the end of the form
        if ($dispatcher->hasListeners(Events::POST_BUILD)) {
            $event = new BuilderEvent($contentType, $metadata, $builder);
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

        /** @var ContentTypeInterface $contentType */
        $contentType = $options['content_type'];

        $metadata = $this->metadataFactory->getMetadata($contentType->getClass());

        if ($dispatcher->hasListeners(Events::PRE_VIEW)) {
            $event = new ViewEvent($contentType, $metadata, $view, $form);
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

        /** @var ContentTypeInterface $contentType */
        $contentType = $options['content_type'];

        $metadata = $this->metadataFactory->getMetadata($contentType->getClass());

        if ($dispatcher->hasListeners(Events::POST_VIEW)) {
            $event = new ViewEvent($contentType, $metadata, $view, $form);
            $event->setOptions($options);

            $this->getEventDispatcher()->dispatch(Events::POST_VIEW, $event);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $dataClassNormalizer = function (Options $options, $value) {
            return $options['content_type']->getClass();
        };

        $contentTypeNormalizer = function (Options $options, $value) {
            if (is_string($value)) {
                $value = $this->resolver->getType($value);
            }

            if (!$value instanceof ContentTypeInterface) {
                throw new InvalidOptionsException(sprintf(
                    'The option "%s" could not be normalized to a valid "%s" object',
                    'content_type',
                    ContentTypeInterface::class
                ));
            }

            return $value;
        };

        $emptyDataNormalizer = function (Options $options, $value) {
            return $options['content_type']->create();
        };

        $resolver
            ->setRequired('content_type')
            ->setAllowedTypes('content_type', [ContentTypeInterface::class, 'string'])
            ->setNormalizer('data_class', $dataClassNormalizer)
            ->setNormalizer('content_type', $contentTypeNormalizer)
            ->setNormalizer('empty_data', $emptyDataNormalizer)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_content';
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
