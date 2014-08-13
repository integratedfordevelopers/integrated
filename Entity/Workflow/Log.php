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

use Symfony\Component\Security\Core\Util\ClassUtils;

use Integrated\Bundle\WorkflowBundle\Entity\Definition;
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
	private $owner;

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
	 * @var Definition\State
	 */
	private $state;

	/**
	 * @var string
	 */
	private $comment = null;

	/**
	 * @var DateTime
	 */
	private $deadline = null;

	public function __construct()
	{
		$this->timestamp = new DateTime();
	}

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
	public function getOwner()
	{
		return $this->state;
	}

	/**
	 * @param State $state
	 * @return $this
	 */
	public function setOwner(State $state = null)
	{
		if ($this->owner !== $state && $this->owner !== null) {
			$this->owner->removeLog($this);
		}

		$this->owner = $state;

		if ($this->owner) {
			$this->owner->addLog($this);
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
	 * @return Definition\State
	 */
	public function getState()
	{
		return $this->state;
	}

	/**
	 * @param Definition\State $state
	 */
	public function setState(Definition\State $state = null)
	{
		$this->state = $state;
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