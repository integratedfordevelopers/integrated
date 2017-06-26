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
use Integrated\Bundle\ContentBundle\Document\Bulk\BulkAction;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class BulkActionListener implements EventSubscriberInterface
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

        if (!$data instanceof BulkAction) {
            return;
        }

        foreach ($data->getActions() as $action) {
            if ($action instanceof RelationAction) {
                if ($action->getReferences()->count() == 0) {
                    $data->removeAction($action);
                }
            }
        }

        $event->setData($data);
    }
}
