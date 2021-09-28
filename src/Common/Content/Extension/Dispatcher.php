<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content\Extension;

use Integrated\Common\Content\ContentInterface;
use Integrated\Common\Content\Extension\Event\ContentEvent;
use Integrated\Common\Content\Extension\Event\ContentTypeEvent;
use Integrated\Common\Content\Extension\Event\MetadataEvent;
use Integrated\Common\Content\Extension\Event\Subscriber\ContentSubscriberInterface;
use Integrated\Common\Content\Extension\Event\Subscriber\ContentTypeSubscriberInterface;
use Integrated\Common\Content\Extension\Event\Subscriber\MetadataSubscriberInterface;
use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\Form\Mapping\MetadataEditorInterface;
use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\ImmutableEventDispatcher;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Dispatcher implements DispatcherInterface, RegistryInterface
{
    /**
     * @var RegistryInterface
     */
    private $registry;

    /**
     * @var EventDispatcherInterface[]
     */
    private $dispatcher = [
        'content' => null,
        'type' => null,
        'metadata' => null,
    ];

    /**
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;

        $this->dispatcher = [
            'content' => new EventDispatcher(),
            'type' => new EventDispatcher(),
            'metadata' => new EventDispatcher(),
        ];

        foreach ($this->registry->getExtensions() as $extension) {
            foreach ($extension->getSubscribers() as $subscriber) {
                if ($subscriber instanceof ContentSubscriberInterface) {
                    $this->dispatcher['content']->addSubscriber($subscriber);
                }

                if ($subscriber instanceof ContentTypeSubscriberInterface) {
                    $this->dispatcher['type']->addSubscriber($subscriber);
                }

                if ($subscriber instanceof MetadataSubscriberInterface) {
                    $this->dispatcher['metadata']->addSubscriber($subscriber);
                }
            }
        }

        $this->dispatcher = [
            'content' => new ImmutableEventDispatcher($this->dispatcher['content']),
            'type' => new ImmutableEventDispatcher($this->dispatcher['type']),
            'metadata' => new ImmutableEventDispatcher($this->dispatcher['metadata']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensions()
    {
        return $this->registry->getExtensions();
    }

    /**
     * {@inheritdoc}
     */
    public function hasExtension($name)
    {
        return $this->registry->hasExtension($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtension($name)
    {
        return $this->registry->getExtension($name);
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch($eventName, $object)
    {
        if ($object instanceof ContentInterface) {
            return $this->dispatcher['content']->dispatch($eventName, new ContentEvent($object));
        }

        if ($object instanceof ContentTypeInterface) {
            return $this->dispatcher['type']->dispatch($eventName, new ContentTypeEvent($object));
        }

        if ($object instanceof MetadataEditorInterface) {
            return $this->dispatcher['metadata']->dispatch($eventName, new MetadataEvent($object));
        }

        return new Event();
    }
}
