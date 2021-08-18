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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Integrated\Bundle\UserBundle\Model\GroupInterface;
use Integrated\Bundle\UserBundle\Model\UserInterface;
use Integrated\Bundle\WorkflowBundle\Entity\Definition;
use Integrated\Common\Content\ContentInterface;
use Symfony\Component\Security\Acl\Util\ClassUtils;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class State
{
    /**
     * @var int
     */
    private $id = null;

    /**
     * @var Definition\State
     */
    private $state;

    /**
     * @var string
     */
    private $content_id;

    /**
     * @var string
     */
    private $content_class;

    /**
     * @var ContentInterface|null
     */
    private $content_instance = null;

    /**
     * @var string
     */
    private $assigned_id = null;

    /**
     * @var string
     */
    private $assigned_class = null;

    /**
     * @var string
     */
    private $assigned_type = null;

    /**
     * @var UserInterface|GroupInterface|null
     */
    private $assigned_instance = null;

    /**
     * @var DateTime
     */
    private $deadline = null;

    /**
     * @var Collection|Log[]
     */
    private $logs;

    public function __construct()
    {
        $this->logs = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     *
     * @return $this
     */
    public function setState(Definition\State $state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return ContentInterface
     */
    public function getContent()
    {
        return $this->content_instance;
    }

    /**
     * @param ContentInterface $content
     *
     * @return $this
     */
    public function setContent($content)
    {
        $this->content_id = null;
        $this->content_class = null;
        $this->content_instance = null;

        if ($content instanceof ContentInterface) {
            $this->content_id = $content->getId();
            $this->content_class = ClassUtils::getRealClass($content);
            $this->content_instance = $content;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getContentId()
    {
        return $this->content_id;
    }

    /**
     * @return string
     */
    public function getContentClass()
    {
        return $this->content_class;
    }

    /**
     * @return GroupInterface|UserInterface
     */
    public function getAssigned()
    {
        return $this->assigned_instance;
    }

    /**
     * @param GroupInterface|UserInterface $assigned
     *
     * @return $this
     */
    public function setAssigned($assigned)
    {
        if ($assigned instanceof UserInterface || $assigned instanceof GroupInterface) {
            $this->assigned_id = $assigned->getId();
            $this->assigned_class = ClassUtils::getRealClass($assigned);
            $this->assigned_type = $assigned instanceof UserInterface ? 'user' : 'group';
            $this->assigned_instance = $assigned;
        } else {
            $this->assigned_id = null;
            $this->assigned_class = null;
            $this->assigned_type = null;
            $this->assigned_instance = null;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getAssignedId()
    {
        return $this->assigned_id;
    }

    /**
     * @return string
     */
    public function getAssignedClass()
    {
        return $this->assigned_class;
    }

    /**
     * @return string
     */
    public function getAssignedType()
    {
        return $this->assigned_type;
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
     *
     * @return $this
     */
    public function setDeadline(DateTime $deadline = null)
    {
        $this->deadline = $deadline;

        return $this;
    }

    /**
     * @return Log[]
     */
    public function getLogs()
    {
        return $this->logs->toArray();
    }

    /**
     * @param log[] $logs
     *
     * @return $this
     */
    public function setLogs(Collection $logs)
    {
        foreach ($this->logs as $log) {
            $this->removeLog($log);
        }

        $this->logs = new ArrayCollection();

        foreach ($logs as $log) {
            $this->addLog($log); // type check
        }

        return $this;
    }

    /**
     * @param Log $log
     *
     * @return $this
     */
    public function addLog(Log $log)
    {
        if (!$this->logs->contains($log)) {
            $this->logs->add($log);

            // first add the log to the state then set the state else
            // there would be a infinite loop

            $log->setOwner($this);
        }

        return $this;
    }

    /**
     * @param Log $log
     *
     * @return $this
     */
    public function removeLog(Log $log)
    {
        if ($this->logs->removeElement($log)) {
            $log->setOwner(null);
        }

        return $this;
    }
}
