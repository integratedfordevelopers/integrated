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

use Integrated\Bundle\ContentBundle\Document\Bulk\Action\RelationAction;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class BulkRelationActionListener implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::SUBMIT => 'onSubmit'
        ];
    }

    /**
     * @param FormEvent $event
     * @return FormEvent|void
     */
    public function onSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (!$data instanceof RelationAction) {
            return;
        }

        if (!$form->getConfig()->hasOption('relation')) {
            return;
        }

        if (!$form->getConfig()->hasOption('handler')) {
            return;
        }

        if (!$data->getReferences()->count()) {
            $event->setData(null);
            return;
        }

        $data
            ->setRelation($form->getConfig()->getOption('relation'))
            ->setName($form->getConfig()->getOption('handler'))
        ;

        $event->setData($data);
    }
}
