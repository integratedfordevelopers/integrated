<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content\Extension;

use Integrated\Common\Content\ExtensibleInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ExtensionEventListener
{
	/**
	 * @var ExtensionInterface
	 */
	protected $extension;

	/**
	 * @var callable
	 */
	protected $listener;

	public function __construct(ExtensionInterface $extension, callable $listener)
	{
		$this->extension = $extension;
		$this->listener = $listener;
	}

	public function __invoke(ExtensionEvent $event, $eventName, EventDispatcherInterface $dispatcher)
	{
		$content = $event->getContent();

		if (!$content || !$this->extension->supportsClass(get_class($content))) {
			return;
		}

		$event = clone $event;
		$event->setData(null);

		if ($content instanceof ExtensibleInterface){
			$event->setData($content->getExtension($this->extension->getName()));
		}

		call_user_func($this->listener, $event, $eventName, $dispatcher);

		if ($content instanceof ExtensibleInterface){
			$content->setExtension($this->extension->getName(), $event->getData());
		}
	}
} 