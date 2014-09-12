<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\EventListener;

use Integrated\Common\Content\Channel\ChannelContextInterface;
use Integrated\Common\Content\Channel\ChannelManagerInterface;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class RequestChannelInjectionListener implements EventSubscriberInterface
{
	/**
	 * @var ChannelManagerInterface
	 */
	private $manager;

	/**
	 * @var ChannelContextInterface
	 */
	private $context;

	/**
	 * @param ChannelManagerInterface $manager
	 * @param ChannelContextInterface $context
	 */
	public function __construct(ChannelManagerInterface $manager, ChannelContextInterface $context)
	{
		$this->manager = $manager;
		$this->context = $context;
	}

	/**
	 * @inheritdoc
	 */
	public static function getSubscribedEvents()
	{
		return [
			KernelEvents::REQUEST => ['onRequest', 34]
		];
	}

	/**
	 * @param GetResponseEvent $event
	 */
	public function onRequest(GetResponseEvent $event)
	{
		$this->getContext()->setChannel($this->getManager()->findByDomain($event->getRequest()->getHost()));
	}

	/**
	 * @return ChannelManagerInterface
	 */
	public function getManager()
	{
		return $this->manager;
	}

	/**
	 * @return ChannelContextInterface
	 */
	public function getContext()
	{
		return $this->context;
	}
}