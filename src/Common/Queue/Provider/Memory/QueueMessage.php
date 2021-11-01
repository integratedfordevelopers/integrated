<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Queue\Provider\Memory;

use Closure;
use Integrated\Common\Queue\Memory\QueueMessageInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class QueueMessage implements QueueMessageInterface
{
    /**
     * @var mixed
     */
    private $payload;

    /**
     * @var int
     */
    private $attempts;

    /**
     * @var int
     */
    private $priority;

    /**
     * @var int
     */
    private $createdAt;

    /**
     * @var int
     */
    private $updatedAt;

    /**
     * @var int
     */
    private $executeAt;

    /**
     * @var Closure|null
     */
    private $release = null;

    /**
     * @param mixed   $payload
     * @param int     $attempts
     * @param int     $priority
     * @param int     $createdAt
     * @param int     $updatedAt
     * @param int     $executeAt
     * @param Closure $release
     */
    public function __construct($payload, $attempts, $priority, $createdAt, $updatedAt, $executeAt, Closure $release)
    {
        $this->payload = $payload;
        $this->attempts = $attempts;
        $this->priority = $priority;

        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->executeAt = $executeAt;

        $this->release = $release;
    }

    /**
     * {@inheritdoc}
     */
    public function delete()
    {
        // release should be cleared as after a delete the message can not
        // be returned anymore;

        $this->release = null;
    }

    /**
     * {@inheritdoc}
     */
    public function release($delay = 0)
    {
        if ($this->release !== null) {
            $release = $this->release;
            $release();
        }

        $this->release = null;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttempts()
    {
        return $this->attempts;
    }

    /**
     * {@inheritdoc}
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt(): int
    {
        return $this->updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getExecuteAt(): int
    {
        return $this->executeAt;
    }
}
