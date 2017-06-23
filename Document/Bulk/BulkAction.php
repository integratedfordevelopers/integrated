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

use Integrated\Bundle\ContentBundle\Bulk\Action\ActionInterface;
use Integrated\Common\Content\ContentInterface;

/**
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
class BulkAction
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $executedAt;

    /**
     * @var string
     */
    private $searchQuery;

    /**
     * @var ArrayCollection|ContentInterface[]
     */
    private $selection;

    /**
     * @var ArrayCollection|ActionInterface[]
     */
    private $actions;

    /**
     * BulkAction constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->selection = new ArrayCollection();
        $this->actions = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
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
     * @return string
     */
    public function getSearchQuery()
    {
        return $this->searchQuery;
    }

    /**
     * @param string $searchQuery
     */
    public function setSearchQuery($searchQuery)
    {
        $this->searchQuery = $searchQuery;
    }

    /**
     * @return ContentInterface[]
     */
    public function getSelection()
    {
        return $this->selection->toArray();
    }

    /**
     * @param ContentInterface[] $contents
     * @return $this
     */
    public function setSelection($contents)
    {
        $this->selection->clear();
        if (is_array($contents) || $contents instanceof \Traversable) {
            foreach ($contents as $content) {
                $this->addSelection($content);
            }
        }
        return $this;
    }

    /**
     * @param ContentInterface $content
     * @return $this
     */
    public function addSelection(ContentInterface $content)
    {
        if (!$this->selection->contains($content)) {
            $this->selection->add($content);
        }

        return $this;
    }

    /**
     * @param ContentInterface $content
     * @return $this
     */
    public function removeSelection(ContentInterface $content)
    {
        $this->selection->removeElement($content);
        return $this;
    }

    /**
     * @return ActionInterface[]
     */
    public function getActions()
    {
        return $this->actions->toArray();
    }

    /**
     * @param ActionInterface[] $actions
     * @return $this
     */
    public function setActions($actions)
    {
        $this->actions->clear();
        if (is_array($actions) || $actions instanceof \Traversable) {
            foreach ($actions as $action) {
                $this->addAction($action);
            }
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
     * @param ActionInterface $action
     * @return $this
     */
    public function removeAction(ActionInterface $action)
    {
        $this->actions->removeElement($action);
        return $this;
    }
}
