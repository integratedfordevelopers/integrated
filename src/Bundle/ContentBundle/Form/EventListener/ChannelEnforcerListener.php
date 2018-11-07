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

use Doctrine\Common\Collections\ArrayCollection;
use Integrated\Bundle\ContentBundle\Document\Channel\Channel;
use Integrated\Common\Content\ChannelableInterface;
use Integrated\Common\Content\Exception\InvalidArgumentException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ChannelEnforcerListener implements EventSubscriberInterface
{
    /**
     * The channels will be set the to content and completely overriding
     * the currently set channels.
     */
    const SET = 'set';

    /**
     * The channels will be added to the content with out overriding the
     * currently set channels.
     */
    const ADD = 'add';

    /**
     * @var Channel[]
     */
    private $channels;

    /**
     * @var string
     */
    private $operand;

    /**
     * @param Channel[] $channels
     * @param string    $operand
     */
    public function __construct(array $channels, $operand = self::SET)
    {
        $this->channels = $channels;

        if (!\in_array($operand, [self::SET, self::ADD])) {
            throw new InvalidArgumentException(sprintf('Valid options are "%s" and "%s", "%s" given', self::SET, self::ADD, $operand));
        }

        $this->operand = $operand;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::POST_SET_DATA => ['onPostSetData', -1],
            FormEvents::POST_SUBMIT => 'onPostSubmit',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function onPostSetData(FormEvent $event)
    {
        $form = $event->getForm();

        if (!$form->has('channels')) {
            return;
        }

        $form->get('channels')->setData(array_merge($form->get('channels')->getData(), $this->channels));
    }

    /**
     * @param FormEvent $event
     */
    public function onPostSubmit(FormEvent $event)
    {
        $data = $event->getData();

        if ($data instanceof ChannelableInterface) {
            switch ($this->operand) {
                case self::SET:
                    $data->setChannels(new ArrayCollection($this->channels));
                    break;

                case self::ADD:
                    foreach ($this->channels as $channel) {
                        $data->addChannel($channel);
                    }
                    break;
            }
        }
    }
}
