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
use Integrated\Bundle\SlugBundle\Mapping\Annotations\Slug;

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
     * @ODM\Id(strategy="NONE")
     * @ODM\UniqueIndex
     * @Slug(fields={"title"})
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
     * @var string
     * @ODM\String
     */
    protected $layout;

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
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->publishedAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * @return \DateTime
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * @param \DateTime $publishedAt
     * @return $this
     */
    public function setPublishedAt(\DateTime $publishedAt = null)
    {
        $this->publishedAt = $publishedAt;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPublishedUntil()
    {
        return $this->publishedUntil;
    }

    /**
     * @param \DateTime $publishedUntil
     * @return Block
     */
    public function setPublishedUntil(\DateTime $publishedUntil = null)
    {
        $this->publishedUntil = $publishedUntil;
        return $this;
    }

    /**
     * @return bool
     */
    public function getDisabled()
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
}
