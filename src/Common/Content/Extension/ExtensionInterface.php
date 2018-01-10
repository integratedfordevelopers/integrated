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

    /**
     * @return string
     */
    public function getName();
}
