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

use Doctrine\Common\EventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class DoctrineOrmAdaptor
{
	/**
	 * @inheritdoc
	 */
	public function getSubscribedEvents()
	{
		return array(
			'preRemove',
			'postRemove',
			'prePersist',
			'postPersist',
			'preUpdate',
			'postUpdate',
			'postLoad',
		);
	}

	protected function getObject(EventArgs $eventArgs)
	{
		if ($eventArgs instanceof LifecycleEventArgs) {
			return $eventArgs->getEntity();
		}

		return null;
	}
} 