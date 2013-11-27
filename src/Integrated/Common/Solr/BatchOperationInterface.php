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
interface BatchOperationInterface
{
	/**
	 * Return the queue message
	 *
	 * @return QueueMessageInterface
	 */
	public function getMessage();

	/**
	 * Get the command
	 *
	 * @return Command|null
	 */
	public function getCommand();

	/**
	 * Set the command
	 *
	 * This allows for the command to be changed or even
	 * to be removed.
	 *
	 * @param Command $command
	 */
	public function setCommand(Command $command = null);
} 