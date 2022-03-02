<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr\Indexer\Event;

use Exception;
use Integrated\Common\Queue\QueueMessageInterface;
use Integrated\Common\Solr\Exception\ExceptionInterface;
use Integrated\Common\Solr\Indexer\IndexerInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ErrorEvent extends MessageEvent
{
    /**
     * @var ExceptionInterface
     */
    private $exception;

    /**
     * Event constructor.
     *
     * @param IndexerInterface      $indexer
     * @param QueueMessageInterface $message
     * @param ExceptionInterface    $exception
     */
    public function __construct(IndexerInterface $indexer, QueueMessageInterface $message, ExceptionInterface $exception)
    {
        parent::__construct($indexer, $message);

        $this->exception = $exception;
    }

    /**
     * Get the exception object for this event.
     *
     * @return ExceptionInterface|Exception
     */
    public function getException()
    {
        return $this->exception;
    }
}
