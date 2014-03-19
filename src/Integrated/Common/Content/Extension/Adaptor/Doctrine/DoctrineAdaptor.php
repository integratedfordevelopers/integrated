<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content\Extension\Adaptor\Doctrine;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\EventArgs;

use Integrated\Common\Content\Extension\Adaptor\AbstractAdaptor;
use Integrated\Common\Content\Extension\ExtensionEvents;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class DoctrineAdaptor extends AbstractAdaptor implements EventSubscriber
{
	/**
	 * @inheritdoc
	 */
	public function getSubscribedEvents()
	{
		return [];
	}

	public function preRemove(EventArgs $eventArgs)
	{
		$this->dispatch(ExtensionEvents::PRE_DELETE, $eventArgs);
	}

	public function postRemove(EventArgs $eventArgs)
	{
		$this->dispatch(ExtensionEvents::POST_DELETE, $eventArgs);
	}

	public function prePersist(EventArgs $eventArgs)
	{
		$this->dispatch(ExtensionEvents::PRE_CREATE, $eventArgs);
	}

	public function postPersist(EventArgs $eventArgs)
	{
		$this->dispatch(ExtensionEvents::POST_CREATE, $eventArgs);
	}

	public function preUpdate(EventArgs $eventArgs)
	{
		$this->dispatch(ExtensionEvents::PRE_UPDATE, $eventArgs);
	}

	public function postUpdate(EventArgs $eventArgs)
	{
		$this->dispatch(ExtensionEvents::POST_UPDATE, $eventArgs);
	}

	public function postLoad(EventArgs $eventArgs)
	{
		$this->dispatch(ExtensionEvents::POST_READ, $eventArgs);
	}

	protected function getObject(EventArgs $eventArgs)
	{
		return null;
	}

	protected function dispatch($event, EventArgs $eventArgs)
	{
		if ($this->dispatcher === null) {
			return;
		}

		if ($object = $this->getObject($eventArgs)) {
			$this->dispatcher->dispatch($event, $object);
		}
	}
}