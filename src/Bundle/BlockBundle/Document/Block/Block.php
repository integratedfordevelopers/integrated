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

use Integrated\Bundle\SlugBundle\Mapping\Annotations\Slug;
use Integrated\Common\Block\BlockInterface;
use Integrated\Common\Form\Mapping\Annotations as Type;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Block document.
 *
 * @author Ger Jan van den Bosch <gerjan@e-active.nl
 */
abstract class Block implements BlockInterface
{
    /**
     * @var string
     * @Slug(fields={"title"}, separator="_")
     */
    protected $id;

    /**
     * @var string
     * @Assert\NotBlank
     * @Type\Field
     */
    protected $title;

    /**
     * @var string
     */
    protected $layout;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var \DateTime
     * @Type\Field(type="Integrated\Bundle\FormTypeBundle\Form\Type\DateTimeType")
     */
    protected $publishedAt;

    /**
     * @var \DateTime
     * @Type\Field(
     *      type="Integrated\Bundle\FormTypeBundle\Form\Type\DateTimeType",
     *      options={
     *          "required"=false
     *      }
     * )
     */
    protected $publishedUntil;

    /**
     * @var bool
     * @Type\Field(
     *      type="Symfony\Component\Form\Extension\Core\Type\CheckboxType",
     *      options={
     *          "required"=false,
     *          "attr"={"align_with_widget"=true}
     *      }
     * )
     */
    protected $disabled = false;

    /**
     * @var bool
     */
    protected $locked = false;

    public function __construct($id = null)
    {
        if ($id) {
            $this->id = $id;
        }
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
     */
    public function setId(string $id)
    {
        $this->id = $id;
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
     *
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
     *
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
     * @return \DateTime
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * @param \DateTime $publishedAt
     *
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
     *
     * @return Block
     */
    public function setPublishedUntil(\DateTime $publishedUntil = null)
    {
        $this->publishedUntil = $publishedUntil;

        return $this;
    }

    /**
     * @param \DateTime $date
     *
     * @return bool
     */
    public function isPublished(\DateTime $date = null)
    {
        if (null === $date) {
            $date = new \DateTime();
        }

        $published = true;

        if ($this->publishedAt && $this->publishedAt > $date) {
            $published = false;
        }

        if ($this->publishedUntil && $this->publishedUntil < $date) {
            $published = false;
        }

        return $published;
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
     *
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
     *
     * @return Block
     */
    public function setLocked($locked)
    {
        $this->locked = (bool) $locked;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getId();
    }
}
