<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WorkflowBundle\Entity\Definition;

use Integrated\Bundle\WorkflowBundle\Entity\Definition;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Log
{
	/**
	 * @var int
	 */
	protected $id = null;

	/**
	 * @var Definition
	 */
	protected $workflow;

	/**
	 * @var \DateTime
	 */
	protected $time;

	protected $user;

	/**
	 * @var string
	 */
	protected $message;

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return Definition
	 */
	public function getWorkflow()
	{
		return $this->workflow;
	}

	/**
	 * @param Definition $workflow
	 * @return $this;
	 */
	public function setWorkflow(Definition $workflow)
	{
		$this->workflow = $workflow;
		return $this;
	}

	/**
	 * @return \DateTime
	 */
	public function getTime()
	{
		return $this->time;
	}

	/**
	 * @param \DateTime $time
	 * @return $this
	 */
	public function setTime($time)
	{
		$this->time = $time;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getMessage()
	{
		return $this->message;
	}

	/**
	 * @param string $message
	 * @return $this
	 */
	public function setMessage($message)
	{
		$this->message = $message;
		return $this;
	}
}