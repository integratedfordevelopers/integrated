<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ChannelBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Symfony\Component\Form\SubmitButton;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ClickedButtonListener implements EventSubscriberInterface
{
    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::SUBMIT => 'onSubmit'
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function onSubmit(FormEvent $event)
    {
        foreach ($event->getForm() as $child) {
            if ($child instanceof SubmitButton && $child->isClicked()) {
                $event->setData($child->getName());
            }
        }
    }
}
