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
        $form = $event->getForm()->getParent();

        if (null !== $form) {
            $content = $form->getNormData();
            if ($content instanceof ExtensibleInterface) {
                // TODO get data from extension
            }
        }

    }

    /**
     * @param FormEvent $event
     */
    public function postSubmit(FormEvent $event)
    {
        $form = $event->getForm()->getParent();

        if (null !== $form) {
            $content = $form->getNormData();
            if ($content instanceof ExtensibleInterface) {
                // TODO set data in extension
            }
        }
    }
}