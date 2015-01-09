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

use Integrated\Bundle\PageBundle\Document\Page\Grid\Grid;

/**
 * Page document
 *
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 *
 * @ODM\Document(collection="page")
 * @MongoDBUnique(fields="slug", message="This value should be unique.")
 */
class Page
{
    /**
     * @var string
     * @ODM\Id(strategy="UUID")
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
     * @ODM\UniqueIndex(sparse=true)
     * @Assert\NotBlank
     */
    protected $slug;

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
     * @var \DateTime
     * @ODM\Date
     */
    protected $publishedAt;

    /**
     * @var bool
     * @ODM\Boolean
     */
    protected $disabled = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->grids = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->publishedAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * Get the id of the document
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the title of the document
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the title of the document
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get the slug of the document
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set the slug of the document
     *
     * @param string $slug
     * @return $this
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * Get the layout of the document
     *
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * Set the layout of the document
     *
     * @param string $layout
     * @return $this
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
        return $this;
    }

    /**
     * Get the grids of the document
     *
     * @return array
     */
    public function getGrids()
    {
        return $this->grids->toArray();
    }

    /**
     * Set the grids of the document
     *
     * @param array $grids
     * @return $this
     */
    public function setGrids(array $grids)
    {
        $this->grids = new ArrayCollection($grids);
        return $this;
    }

    /**
     * Add grid to the document
     *
     * @param Grid $grid
     * @return $this
     */
    public function addGrid(Grid $grid)
    {
        $this->grids->add($grid);
        return $this;
    }

    /**
     * Remove grid of the document
     *
     * @param Grid $grid
     * @return $this
     */
    public function removeGrid(Grid $grid)
    {
        $this->grids->removeElement($grid);
        return $this;
    }

    /**
     * Get the created at of the document
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set the created at of the document
     *
     * @param \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Get the updated at of the document
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set the updated at of the document
     *
     * @param \DateTime $updatedAt
     * @return $this
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * Get the published at of the document
     *
     * @return \DateTime
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * Set the published at of the document
     *
     * @param \DateTime $publishedAt
     * @return $this
     */
    public function setPublishedAt(\DateTime $publishedAt = null)
    {
        $this->publishedAt = $publishedAt;
        return $this;
    }

    /**
     * Get the disabled of the document
     *
     * @return bool
     */
    public function getDisabled()
    {
        return $this->disabled;
    }

    /**
     * Set the disabled of the document
     *
     * @param bool $disabled
     * @return $this
     */
    public function setDisabled($disabled)
    {
        $this->disabled = (bool) $disabled;
        return $this;
    }
}
