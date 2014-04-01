<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Integrated\Common\Content\ExtensibleInterface;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class UserSubscriber implements EventSubscriberInterface
{
    /**
     * @var string
     */
    protected $extensionName;

    /**
     * @param string $extensionName
     */
    public function __construct($extensionName)
    {
        $this->extensionName = $extensionName;
    }

    /**
     * @param string $extensionName
     * @return $this
     */
    public function setExtensionName($extensionName)
    {
        $this->extensionName = $extensionName;
        return $this;
    }

    /**
     * @return string
     */
    public function getExtensionName()
    {
        return $this->extensionName;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::POST_SUBMIT => 'postSubmit'
        );
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $parent = $event->getForm()->getParent();

        if (null !== $parent) {
            $content = $parent->getNormData();
            if ($content instanceof ExtensibleInterface) {
                $event->setData($content->getExtension($this->extensionName));
            }
        }

    }

    /**
     * @param FormEvent $event
     */
    public function postSubmit(FormEvent $event)
    {
        $parent = $event->getForm()->getParent();

        if (null !== $parent) {
            $content = $parent->getNormData();
            if ($content instanceof ExtensibleInterface) {
                $content->setExtension($this->extensionName, $event->getForm()->getData());
            }
        }
    }
}