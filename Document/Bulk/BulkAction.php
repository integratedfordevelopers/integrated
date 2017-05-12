<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Bundle\ContentBundle\Document\Bulk;

use Doctrine\Common\Collections\ArrayCollection;
use Integrated\Bundle\ContentBundle\Bulk\ActionInterface;
use Integrated\Bundle\ContentBundle\Bulk\BuildState;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Common\Form\Mapping\Annotations as Type;

/**
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
class BulkAction
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var int
     */
    protected $state;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $executedAt;

    /**
     * @var ArrayCollection
     */
    protected $selection;

    /**
     * @var ArrayCollection
     */
    protected $actions;

    /**
     * BulkAction constructor.
     * @param $selection
     */
    public function __construct($selection)
    {
        $this->createdAt = new \DateTime();
        $this->selection = new ArrayCollection($selection);
        $this->actions = new ArrayCollection();
        $this->state = BuildState::SELECTED;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param int $state
     */
    public function setState($state)
    {
        switch ($state) {
            case BuildState::SELECTED:
                $this->state = BuildState::SELECTED;
                break;
            case BuildState::CONFIGURED:
                $this->state = BuildState::CONFIGURED;
                break;
            case BuildState::CONFIRMED:
                $this->state = BuildState::CONFIRMED;
                break;
            case BuildState::EXECUTED:
                $this->state = BuildState::EXECUTED;
                break;
            default:
                throw new \RuntimeException("State does not exist.");
        }
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getExecutedAt()
    {
        return $this->executedAt;
    }

    /**
     * @param \DateTime $executedAt
     * @return $this
     */
    public function setExecutedAt(\DateTime $executedAt)
    {
        $this->executedAt = $executedAt;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getSelection()
    {
        return $this->selection;
    }

    /**
     * @param array $contents
     * @return $this
     */
    public function setSelection(array $contents)
    {
        $this->selection = new ArrayCollection($contents);

        return $this;
    }


    /**
     * @return ArrayCollection
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param ArrayCollection $actions
     * @return $this
     */
    public function setActions(ArrayCollection $actions)
    {
        $notAllActions = $actions->exists(function ($key, $element) {
            return !$element instanceof ActionInterface;
        });

        if ($notAllActions) {
            throw new \RuntimeException('Items in ArrayCollection do not all implement ' . ActionInterface::class);
        }

        $this->actions = $actions;
        return $this;
    }


    /**
     * @param ArrayCollection $actions
     * @return $this
     */
    public function addActions(ArrayCollection $actions)
    {
        foreach ($actions as $action) {
            $this->addAction($action);
        }
        return $this;
    }

    /**
     * @param ActionInterface $action
     * @return $this
     */
    public function addAction(ActionInterface $action)
    {
        if (!$this->actions->contains($action)) {
            $this->actions->add($action);
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function executeAll()
    {
        if ($this->getState() !== BuildState::CONFIRMED) {
            throw new \RuntimeException("Its seems not all steps have been completed.");
        }

        foreach ($this->getSelection() as $content) {
            $this->executeActionsOnContent($content);
        }

        $this->setState(BuildState::EXECUTED);
        $this->setExecutedAt(new \DateTime());

        return $this;
    }

    /**
     * @param Content $content
     * @return $this
     */
    protected function executeActionsOnContent(Content $content)
    {
        foreach ($this->getActions() as $action) {
            if ($action instanceof ActionInterface) {
                $action->execute($content);
            }
        }
        return $this;
    }
}
