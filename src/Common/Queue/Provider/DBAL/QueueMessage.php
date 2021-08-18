<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Queue\Provider\DBAL;

use Closure;
use Integrated\Common\Queue\QueueMessageInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class QueueMessage implements QueueMessageInterface
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @var mixed|null
     */
    private $payload = null;

    /**
     * @var Closure|null
     */
    private $delete;

    /**
     * @var Closure|null
     */
    private $release;

    /**
     * @param array    $data
     * @param callable $delete
     * @param callable $release
     */
    public function __construct(array $data, Closure $delete, Closure $release)
    {
        $this->data = $data;

        $this->delete = $delete;
        $this->release = $release;
    }

    /**
     * {@inheritdoc}
     */
    public function delete()
    {
        if ($this->delete !== null) {
            $delete = $this->delete;
            $delete();
        }

        $this->delete = null;

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
            $release($delay);
        }

        $this->release = null;

        // delete should be cleared as the message has been released the control
        // is given back to the provider.

        $this->delete = null;
    }

    /**
     * Get the message id.
     *
     * @return string
     */
    public function getId()
    {
        return (string) $this->data['id'];
    }

    /**
     * {@inheritdoc}
     */
    public function getAttempts()
    {
        return (int) $this->data['attempts'];
    }

    /**
     * {@inheritdoc}
     */
    public function getPayload()
    {
        if ($this->payload === null) {
            $this->payload = unserialize($this->data['payload']);
        }

        return $this->payload;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return (int) $this->data['priority'];
    }

    /**
     * Get all the message data.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
