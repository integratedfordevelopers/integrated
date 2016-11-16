<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr\Indexer;

use Integrated\Common\Queue\QueueMessageInterface;

use Solarium\QueryType\Update\Query\Command\AbstractCommand;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class BatchOperation
{
    /**
     * @var QueueMessageInterface
     */
    private $message;

    /**
     * @var AbstractCommand|null
     */
    private $command = null;

    /**
     * Create a batch operation.
     *
     * @param QueueMessageInterface $message
     * @param AbstractCommand $command
     */
    public function __construct(QueueMessageInterface $message, AbstractCommand $command = null)
    {
        $this->message = $message;
        $this->command = $command;
    }

    /**
     * Return the queue message.
     *
     * @return QueueMessageInterface
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Get the command.
     *
     * @return Command|null
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * Set the command.
     *
     * This allows for the command to be changed or even
     * to be removed.
     *
     * @param Command $command
     */
    public function setCommand(Command $command = null)
    {
        $this->command = $command;
    }
}
