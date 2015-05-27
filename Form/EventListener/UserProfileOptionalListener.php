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

use Integrated\Common\Content\Extension\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class UserProfileOptionalListener implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::POST_SET_DATA => 'onPostSetData',
            FormEvents::POST_SUBMIT => 'onPostSubmit'
        );
    }

    /**
     * @param FormEvent $event
     */
    public function onPostSetData(FormEvent $event)
    {
        $data = $event->getData();

        if ($data === null) {
            return;
        }

        if ($data instanceof AdvancedUserInterface) {
            $event->getForm()->get('enabled')->setData($data->isEnabled());
        }
    }

    /**
     * @param FormEvent $event
     */
    public function onPostSubmit(FormEvent $event)
    {
        $data = $event->getData();

        if ($data === null) {
            return;
        }

        if ($data instanceof AdvancedUserInterface) {
            $data->setEnabled($event->getForm()->get('enabled')->getData());
        }
    }
}
