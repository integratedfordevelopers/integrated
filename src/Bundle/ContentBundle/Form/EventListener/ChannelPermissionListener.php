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

use Integrated\Bundle\ContentBundle\Document\Channel\Channel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ChannelPermissionListener implements EventSubscriberInterface
{
    /**
     * @var Channel[]
     */
    private $notPermittedChannels;

    /**
     * @param Channel[] $notPermittedChannels
     */
    public function __construct(array $notPermittedChannels)
    {
        $this->notPermittedChannels = $notPermittedChannels;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SUBMIT => 'onPreSubmit',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function onPreSubmit(FormEvent $event)
    {
        if (!\count($this->notPermittedChannels)) {
            return;
        }

        $data = $event->getData();

        if (isset($data['channels']) && \is_array($data['channels'])) {
            foreach ($data['channels'] as $key => $value) {
                if (isset($this->notPermittedChannels[$value])) {
                    unset($data['channels'][$key]);
                }
            }
        }

        $event->setData($data);
    }
}
