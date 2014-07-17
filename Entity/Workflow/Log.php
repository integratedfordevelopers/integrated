<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WorkflowBundle\Entity\Workflow;

use DateTime;

use Doctrine\Common\Util\ClassUtils;

use Integrated\Bundle\UserBundle\Model\UserInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Log
{
	/**
	 * @var int
	 */
	private $id = null;

	/**
	 * @var State
	 */
	private $state;

	/**
	 * @var DateTime
	 */
	private $timestamp;

	/**
	 * @var string
	 */
	private $user_id;

	/**
	 * @var string
	 */
	private $user_class;

	/**
	 * @var UserInterface
	 */
	private $user_instance;

	/**
	 * @var string
	 */
	private $comment = null;

	/**
	 * @var DateTime
	 */
	private $deadline = null;

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return State
	 */
	public function getState()
	{
		return $this->state;
	}

	/**
	 * @param State $state
	 * @return $this
	 */
	public function setState(State $state = null)
	{
		if ($this->state !== $state && $this->state !== null) {
			$this->state->removeLog($this);
		}

		$this->state = $state;

		if ($this->state) {
			$this->state->addLog($this);
		}

		return $this;
	}

	/**
	 * @return DateTime
	 */
	public function getTimestamp()
	{
		return $this->timestamp;
	}

	/**
	 * @param DateTime $timestamp
	 * @return $this
	 */
	public function setTimestamp(DateTime $timestamp = null)
	{
		$this->timestamp = $timestamp;
		return $this;
	}

	/**
	 * @return UserInterface
	 */
	public function getUser()
	{
		return $this->user_instance;
	}

	/**
	 * @param UserInterface $user
	 * @return $this
	 */
	public function setUser($user)
	{
		if ($user instanceof UserInterface)	{
			$this->user_id = $user->getId();
			$this->user_class = ClassUtils::getRealClass($user);
			$this->user_instance = $user;
		} else {
			$this->user_id = null;
			$this->user_class = null;
			$this->user_instance = null;
		}

		return $this;
	}

	/**
	 * @return string
	 */
	public function getUserId()
	{
		return $this->user_id;
	}

	/**
	 * @return string
	 */
	public function getUserClass()
	{
		return $this->user_class;
	}

	/**
	 * @return string
	 */
	public function getComment()
	{
		return $this->comment;
	}

	/**
	 * @param string $comment
	 * @return $this
	 */
	public function setComment($comment)
	{
		$this->comment = $comment;
		return $this;
	}

	/**
	 * @return DateTime
	 */
	public function getDeadline()
	{
		return $this->deadline;
	}

	/**
	 * @param DateTime $deadline
	 * @return $this
	 */
	public function setDeadline(DateTime $deadline = null)
	{
		$this->deadline = $deadline;
		return $this;
	}
}