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

use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ExtensionEventDispatcher extends EventDispatcher
{
	public function addListener($eventName, $listener, $priority = 0)
	{
		if (is_array($listener) && $listener[0] instanceof ExtensionInterface) {
			$listener = new ExtensionEventListener($listener[0], $listener);
		}

		parent::addListener($eventName, $listener, $priority);
	}
}