<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr\Task\Event;

use Integrated\Common\Solr\Task\Worker;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class WorkerEvent extends Event
{
    /**
     * @var Worker
     */
    private $worker;

    /**
     * Event constructor.
     *
     * @param Worker $worker
     */
    public function __construct(Worker $worker)
    {
        $this->worker = $worker;
    }

    /**
     * Get the worker instance for this event.
     *
     * @return Worker
     */
    public function getWorker()
    {
        return $this->worker;
    }
}
