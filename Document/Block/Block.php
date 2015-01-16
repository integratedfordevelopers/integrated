<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\BlockBundle\Document\Block;

use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

use Integrated\Common\Block\BlockInterface;
use Integrated\Common\Form\Mapping\Annotations as Type;

/**
 * Block document
 *
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 *
 * @ODM\Document(collection="block", indexes={@ODM\Index(keys={"class"="asc"})})
 * @ODM\InheritanceType("SINGLE_COLLECTION")
 * @ODM\DiscriminatorField(fieldName="class")
 */
abstract class Block implements BlockInterface
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
     * @Type\Field
     */
    protected $title;

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
     * @Type\Field(type="integrated_datetime")
     */
    protected $publishedAt;

    /**
     * @var \DateTime
     * @ODM\Date
     * @Type\Field(type="integrated_datetime")
     */
    protected $publishedUntil;

    /**
     * @var bool
     * @ODM\Boolean
     * @Type\Field(
     *      type="checkbox",
     *      options={
     *          "required"=false
     *      }
     * )
     */
    protected $disabled = false;

    /**
     * Constructor
     */
    public function __construct()
    {
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
     * Get the updated a of the document
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
     * Get the published until of the document
     *
     * @return \DateTime
     */
    public function getPublishedUntil()
    {
        return $this->publishedUntil;
    }

    /**
     * Set the published until of the document
     *
     * @param \DateTime $publishedUntil
     * @return Block
     */
    public function setPublishedUntil(\DateTime $publishedUntil = null)
    {
        $this->publishedUntil = $publishedUntil;
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
