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

use Exception;
use Integrated\Common\Queue\QueueMessageInterface;
use Integrated\Common\Solr\Task\Worker;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ErrorEvent extends WorkerEvent
{
    /**
     * @var QueueMessageInterface
     */
    protected $message;

    /**
     * @var Exception
     */
    private $exception;

    /**
     * Event constructor.
     *
     * @param Worker                $worker
     * @param QueueMessageInterface $message
     * @param Exception             $exception
     */
    public function __construct(Worker $worker, QueueMessageInterface $message, Exception $exception)
    {
        parent::__construct($worker);

        $this->message = $message;
        $this->exception = $exception;
    }

    /**
     * Get the queue message instance for this event.
     *
     * @return QueueMessageInterface
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Get the exception instance for this event.
     *
     * @return Exception
     */
    public function getException()
    {
        return $this->exception;
    }
}
