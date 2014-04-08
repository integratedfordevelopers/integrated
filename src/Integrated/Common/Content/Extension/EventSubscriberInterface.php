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

use Symfony\Component\EventDispatcher\EventSubscriberInterface AS BaseEventSubscriberInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface EventSubscriberInterface extends BaseEventSubscriberInterface
{
	/**
	 * @return ExtensionInterface
	 */
	public function getExtension();
}