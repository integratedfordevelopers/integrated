<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\PageBundle\Document\Page;

use Doctrine\Common\Collections\ArrayCollection;
use Integrated\Bundle\ContentBundle\Document\Channel\Channel;
use Integrated\Bundle\PageBundle\Document\Page\Grid\Grid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Page document.
 *
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 *
 * @todo find a way to fix unique validation (INTEGRATED-481)
 */
abstract class AbstractPage
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     * @Assert\NotBlank
     */
    protected $path;

    /**
     * @var string
     * @Assert\NotBlank
     */
    protected $layout;

    /**
     * @var Grid[]
     */
    protected $grids;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var Channel
     */
    protected $channel;

    public function __construct()
    {
        $this->grids = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     *
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @param string $layout
     *
     * @return $this
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;

        return $this;
    }

    /**
     * @return Grid[]
     */
    public function getGrids()
    {
        return $this->grids->toArray();
    }

    /**
     * @param array $grids
     *
     * @return $this
     */
    public function setGrids(array $grids)
    {
        $this->grids = new ArrayCollection($grids);

        return $this;
    }

    /**
     * @param Grid $grid
     *
     * @return $this
     */
    public function addGrid(Grid $grid)
    {
        $this->grids->add($grid);

        return $this;
    }

    /**
     * @param Grid $grid
     *
     * @return $this
     */
    public function removeGrid(Grid $grid)
    {
        $this->grids->removeElement($grid);

        return $this;
    }

    /**
     * @param Grid $grid
     *
     * @return int
     */
    public function indexOf(Grid $grid)
    {
        return $this->grids->indexOf($grid);
    }

    /**
     * @param string $id
     *
     * @return Grid|null
     */
    public function getGrid($id)
    {
        foreach ($this->grids as $grid) {
            if ($grid instanceof Grid && $grid->getId() == $id) {
                return $grid;
            }
        }

        return null;
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
     *
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
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Channel
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @param Channel $channel
     *
     * @return $this
     */
    public function setChannel(Channel $channel)
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getPath();
    }
}
