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
use Integrated\Common\Form\Mapping\Metadata\Field;
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
     * @param ResolverInterface        $resolver
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

        /** @var ContentTypeInterface $type */
        $type = $options['content_type'];
        unset($options['content_type']);

        $metadata = $this->metadataFactory->getMetadata($type->getClass());

        // Allow events to change the options or add fields at the start of the form
        if ($dispatcher->hasListeners(Events::PRE_BUILD)) {
            $options = $dispatcher->dispatch(new BuilderEvent(
                $type,
                $metadata,
                $builder,
                $options
            ),
            Events::PRE_BUILD)->getOptions();
        }

        foreach ($metadata->getFields() as $field) {
            // Allow events to add fields before the supplied field
            if ($dispatcher->hasListeners(Events::PRE_BUILD_FIELD)) {
                $dispatcher->dispatch(new BuilderEvent(
                    $type,
                    $metadata,
                    $builder,
                    $options,
                    $field->getName()
                ),
                Events::PRE_BUILD_FIELD);
            }

            if ($type->hasField($field->getName())) {
                $config = new Field($field->getName());

                $config->setType($field->getType());
                $config->setOptions($type->getField($field->getName())->getOptions() + $field->getOptions());

                // Allow events to change the supplied field options or even remove it from the form
                if ($dispatcher->hasListeners(Events::BUILD_FIELD)) {
                    $event = new FieldEvent($type, $metadata, $config, $options);
                    $event->setData($builder->getData());

                    if ($dispatcher->dispatch($event, Events::BUILD_FIELD)->isIgnored()) {
                        $config = null;
                    }
                }

                if ($config) {
                    $builder->add($config->getName(), $config->getType(), $config->getOptions());
                }
            }

            // Allow events to add fields after the supplied field
            if ($dispatcher->hasListeners(Events::POST_BUILD_FIELD)) {
                $dispatcher->dispatch(new BuilderEvent(
                    $type,
                    $metadata,
                    $builder,
                    $options,
                    $field->getName()
                ),
                Events::POST_BUILD_FIELD);
            }
        }

        // Allow events to add fields at the end of the form
        if ($dispatcher->hasListeners(Events::POST_BUILD)) {
            $dispatcher->dispatch(new BuilderEvent($type, $metadata, $builder, $options), Events::POST_BUILD);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $dispatcher = $this->getEventDispatcher();

        if (!$dispatcher->hasListeners(Events::PRE_VIEW)) {
            return;
        }

        /** @var ContentTypeInterface $type */
        $type = $options['content_type'];
        unset($options['content_type']);

        $dispatcher->dispatch(new ViewEvent(
            $type,
            $this->metadataFactory->getMetadata($type->getClass()),
            $view,
            $form,
            $options
        ),
        Events::PRE_VIEW);
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $dispatcher = $this->getEventDispatcher();

        if (!$dispatcher->hasListeners(Events::POST_VIEW)) {
            return;
        }

        /** @var ContentTypeInterface $type */
        $type = $options['content_type'];
        unset($options['content_type']);

        $dispatcher->dispatch(new ViewEvent(
            $type,
            $this->metadataFactory->getMetadata($type->getClass()),
            $view,
            $form,
            $options
        ),
        Events::POST_VIEW);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $contentTypeNormalizer = function (Options $options, $value) {
            if (\is_string($value)) {
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

        $dataClassNormalizer = function (Options $options, $value) {
            return $options['content_type']->getClass();
        };

        $emptyDataNormalizer = function (Options $options, $value) {
            return $options['content_type']->create();
        };

        $resolver
            ->setRequired('content_type')
            ->setAllowedTypes('content_type', [ContentTypeInterface::class, 'string'])
            ->setNormalizer('content_type', $contentTypeNormalizer)
            ->setNormalizer('data_class', $dataClassNormalizer)
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
