<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr;

use Solarium\QueryType\Update\Query\Command\Command;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class BatchOperation implements BatchOperationInterface
{
	/**
	 * @var QueueMessageInterface
	 */
	private $message;

	/**
	 * @var Command | null
	 */
	private $command = null;

	/**
	 * Create a batch operation
	 *
	 * @param QueueMessageInterface $message
	 * @param Command $command
	 */
	public function __construct(QueueMessageInterface $message, Command $command = null)
	{
		$this->message = $message;
		$this->command = $command;
	}

	/**
	 * @inheritdoc
	 */
	public function getMessage()
	{
		return $this->message;
	}

	/**
	 * @inheritdoc
	 */
	public function getCommand()
	{
		$this->command;
	}

	/**
	 * @inheritdoc
	 */
	public function setCommand(Command $command = null)
	{
		$this->command = $command;
	}
}