<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\PageBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Integrated\Bundle\PageBundle\Document\Page\Grid\Column;
use Integrated\Bundle\PageBundle\Document\Page\Grid\Grid;
use Integrated\Bundle\PageBundle\Document\Page\Grid\Item;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class ItemOrderListener implements EventSubscriberInterface
{
    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::POST_SUBMIT => 'onPostSubmit'
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function onPostSubmit(FormEvent $event)
    {
        $data = $event->getData();

        if ($data instanceof Grid || $data instanceof Column) {

            $items = $data->getItems();

            usort($items, function($a, $b) {

                if (!$a instanceof Item || !$b instanceof Item || $a->getOrder() == $b->getOrder()) {
                    return 0;
                }

                return $a->getOrder() < $b->getOrder() ? -1 : 1;
            });

            $data->setItems($items);
        }
    }
}