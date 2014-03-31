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
interface ExtensionInterface
{
	/**
	 * @return EventSubscriberInterface
	 */
	public function getEventSubscriber();

	/**
	 * @return string
	 */
	public function getName();

//	/**
//	 * @return string
//	 */
//	public function getDescription();
}

//interface ConfigurableInterface
//{
//	/**
//	 * @return ConfigInterface
//	 */
//	public function getConfig();
//}
//
//interface ConfigInterface
//{
//	public function getOptions();
//
//	public function setOptions(array $options);
//
//	public function setOption($option, $value = null);
//
//	public function getOption($option);
//
//	public function hasOption($option);
//
//	public function getDefaults();
//
//	public function getDefault($option);
//
//	public function hasDefault($option);
//}





