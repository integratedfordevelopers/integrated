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
use Integrated\Bundle\ContentBundle\Form\Type\FormConfigFieldOptions;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class FormConfigFieldOptionsListener implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'updateOptions',
            FormEvents::PRE_SUBMIT => 'updateOptionsAfterSubmit',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function updateOptions(FormEvent $event)
    {
        $form = $event->getForm();

        if (!$form->has('options')) {
            $form->add('options', FormConfigFieldOptions::class);
        }

        $data = $event->getData();

        if ($data && !$data instanceof CustomField) {
            $form->remove('options');
        }
    }

    /**
     * @param FormEvent $event
     */
    public function updateOptionsAfterSubmit(FormEvent $event)
    {
        $form = $event->getForm();

        if (!$form->has('options')) {
            $form->add('options', FormConfigFieldOptions::class);
        }

        $data = $event->getData();

        if ($data && $data['type'] !== 'custom') {
            $form->remove('options');
        }
    }
}
