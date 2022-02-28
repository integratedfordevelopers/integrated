<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr\Task;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
final class Events
{
    /**
     * This event is fired just before the worker run is started.
     *
     * The event listener receives a WorkerEvent instance.
     *
     * @var string
     */
    public const PRE_EXECUTE = 'integrated.solr.task.preExecute';

    /**
     * This event is fired just before the worker run is finished.
     *
     * The event listener receives a WorkerEvent instance.
     *
     * @var string
     */
    public const POST_EXECUTE = 'integrated.solr.task.postExecute';

    /**
     * This event is fired after a error occurred when executing a
     * task. The processing of the task will be halted after this and
     * the task will be removed from the queue.
     *
     * The event listener receives a ErrorEvent instance.
     *
     * @var string
     */
    public const ERROR = 'integrated.solr.task.error';
}
