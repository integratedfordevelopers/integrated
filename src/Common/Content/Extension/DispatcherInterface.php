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

use Symfony\Contracts\EventDispatcher\Event;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface DispatcherInterface
{
    /**
     * Only ContentInterface objects will be processed by the dispatch function.
     *
     * @param string $event
     * @param object $object
     *
     * @return Event
     */
    public function dispatch($event, $object);
}
