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
use Doctrine\Common\Collections\Collection;

use Integrated\Bundle\ContentBundle\Document\Channel\Channel;

use Integrated\Common\Content\ChannelableInterface;
use Integrated\Common\Content\ContentInterface;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ChannelDefaultDataListener implements EventSubscriberInterface
{
	/**
	 * @var Channel[]
	 */
	private $channels;

	/**
	 * @param Channel[] $channels
	 */
	public function __construct(array $channels)
	{
		$this->channels = $channels;
	}

	/**
	 * @inheritdoc
	 */
	public static function getSubscribedEvents()
	{
		return [
			FormEvents::POST_SET_DATA => 'onPostSetData'
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

		$data = $event->getData();

		// If the data is a content type then check if its a new one. This can be done
		// by checking if the id is null since a content item that is just created will
		// have no id set.

		if ($data instanceof ContentInterface && $data instanceof ChannelableInterface && $data->getId() === null) {
			$channels = $data->getChannels();

			// Only set the default channels if no other channels are set, so if the current
			// channel Collection or array is empty

			if (($channels instanceof Collection && $channels->isEmpty()) || empty($channels)) {
				$form->get('channels')->setData($this->channels);
			}
		}

		// If the data is empty then we can be sure this is a new item so not need to do
		// other checks and just set the default channels

		if ($data === null || $data === "") {
			$form->get('channels')->setData($this->channels);
		}
	}
}
