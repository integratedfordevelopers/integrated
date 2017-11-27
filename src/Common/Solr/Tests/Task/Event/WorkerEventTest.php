<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr\Tests\Task\Event;

use Integrated\Common\Solr\Task\Event\WorkerEvent;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class WorkerEventTest extends AbstractEventTest
{
    protected function getInstance()
    {
        return new WorkerEvent($this->worker);
    }
}
