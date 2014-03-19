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

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface ExtensionInterface extends EventSubscriberInterface
{
	/**
	 * @return string
	 */
	public function getName();

	/**
	 * @param string $class
	 * @return bool
	 */
	public function supportsClass($class);
}