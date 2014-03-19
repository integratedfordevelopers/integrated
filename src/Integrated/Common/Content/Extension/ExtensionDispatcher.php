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

use Integrated\Common\Content\ContentInterface;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\ImmutableEventDispatcher;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ExtensionDispatcher implements ExtensionDispatcherInterface, ExtensionRegistryInterface
{
	/**
	 * @var ExtensionRegistryInterface
	 */
	private $registry;

	/**
	 * @var EventDispatcherInterface
	 */
	private $dispatcher;

	/**
	 * @param ExtensionRegistryInterface $registry
	 */
	public function __construct(ExtensionRegistryInterface $registry)
	{
		$this->registry   = $registry;
		$this->dispatcher = new ExtensionEventDispatcher();

		foreach ($this->registry->getExtensions() as $extension) {
			$this->dispatcher->addSubscriber($extension);
		}

		$this->dispatcher = new ImmutableEventDispatcher($this->dispatcher);
	}

	/**
	 * @inheritdoc
	 */
	public function getExtensions()
	{
		return $this->registry->getExtensions();
	}

	/**
	 * @inheritdoc
	 */
	public function hasExtension($name)
	{
		return $this->registry->hasExtension($name);
	}

	/**
	 * @inheritdoc
	 */
	public function getExtension($name)
	{
		return $this->registry->getExtension($name);
	}

	/**
	 * @inheritdoc
	 */
	public function dispatch($eventName, $object)
	{
		$event = new ExtensionEvent();

		if ($object instanceof ContentInterface) {
			$event->setContent($object);
			$event = $this->dispatcher->dispatch($eventName, $event);
		}

		return $event;
	}
}