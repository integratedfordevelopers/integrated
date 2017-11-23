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

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface ExtensionInterface
{
    /**
     * Return a list of event subscribers to add to the event dispatcher.
     *
     * @return EventSubscriberInterface[]
     */
    public function getSubscribers();

    //	/**
    //	 * @return Dependency
    //	 */
    //	public function getDependencies();

    /**
     * @return string
     */
    public function getName();

    //	/**
//	 * @return string
//	 */
//	public function getDescription();
//
//	/**
//	 * @return string
//	 */
//	public function getVersion();
}

//interface EventSubscriberInterface extends BaseEventSubscriberInterface
//{
//	public function
//
//	public function isSupported($class);
//
//	public function isDisabled($class);
//}
//
//interface Dependency
//{
//	public function required();
//
//	public function optional();
//}
//
//interface Specification
//{
//	public function required();
//
//	public function optional();
//}
