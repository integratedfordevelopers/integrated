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

use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique as MongoDBUnique;

use Integrated\Bundle\SlugBundle\Mapping\Annotations\Slug;
use Integrated\Bundle\ContentBundle\Document\Channel\Channel;
use Integrated\Bundle\PageBundle\Document\Page\Grid\Grid;

/**
 * Page document
 *
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 *
 * @ODM\Document(collection="page")
 * @todo find a way to fix unique validation (INTEGRATED-481)
 */
class Page
{
    /**
     * @var string
     * @ODM\Id(strategy="NONE")
     * @Slug(fields={"title"})
     */
    protected $id;

    /**
     * @var string
     * @ODM\String
     * @Assert\NotBlank
     */
    protected $title;

    /**
     * @var string
     * @ODM\String
     */
    protected $description;

    /**
     * @var string
     * @ODM\String
     * @Assert\NotBlank
     */
    protected $path;

    /**
     * @var string
     * @ODM\String
     * @Assert\NotBlank
     */
    protected $layout;

    /**
     * @var array
     * @ODM\EmbedMany(targetDocument="Integrated\Bundle\PageBundle\Document\Page\Grid\Grid")
     */
    protected $grids;

    /**
     * @var \DateTime
     * @ODM\Date
     */
    protected $createdAt;

    /**
     * @var \DateTime
     * @ODM\Date
     */
    protected $updatedAt;

    /**
     * @var bool
     * @ODM\Boolean
     */
    protected $disabled = false;

    /**
     * @var bool
     * @ODM\Boolean
     */
    protected $locked = false;

    /**
     * @var Channel
     * @ODM\ReferenceOne(targetDocument="Integrated\Bundle\ContentBundle\Document\Channel\Channel")
     */
    protected $channel;

    /**
     */
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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
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
     * @return $this
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
        return $this;
    }

    /**
     * @return array
     */
    public function getGrids()
    {
        return $this->grids->toArray();
    }

    /**
     * @param array $grids
     * @return $this
     */
    public function setGrids(array $grids)
    {
        $this->grids = new ArrayCollection($grids);
        return $this;
    }

    /**
     * @param Grid $grid
     * @return $this
     */
    public function addGrid(Grid $grid)
    {
        $this->grids->add($grid);
        return $this;
    }

    /**
     * @param Grid $grid
     * @return $this
     */
    public function removeGrid(Grid $grid)
    {
        $this->grids->removeElement($grid);
        return $this;
    }

    /**
     * @param Grid $grid
     * @return int
     */
    public function indexOf(Grid $grid)
    {
        return $this->grids->indexOf($grid);
    }

    /**
     * @param string $id
     * @return Grid
     */
    public function getGrid($id)
    {
        foreach ($this->grids as $grid) {
            if ($grid instanceof Grid && $grid->getId() == $id) {
                return $grid;
            }
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
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     * @return $this
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDisabled()
    {
        return $this->disabled;
    }

    /**
     * @param bool $disabled
     * @return $this
     */
    public function setDisabled($disabled)
    {
        $this->disabled = (bool) $disabled;
        return $this;
    }

    /**
     * @return bool
     */
    public function isLocked()
    {
        return $this->locked;
    }

    /**
     * @param bool $locked
     * @return $this
     */
    public function setLocked($locked)
    {
        $this->locked = (bool) $locked;
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
     * @return $this
     */
    public function setChannel(Channel $channel)
    {
        $this->channel = $channel;
        return $this;
    }
}
