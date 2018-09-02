<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Document\Content;

use Integrated\Common\Form\Mapping\Annotations as Type;

/**
 * Document type Comment.
 *
 * @author Koen Prins <koen@e-active.nl>
 *
 * @Type\Document("Comment")
 */
class Comment extends Content
{
    /**
     * @var string
     * @Type\Field
     */
    protected $title;

    /**
     * @var string
     * @Type\Field
     */
    protected $name;

    /**
     * @var string
     * @Type\Field(type="Symfony\Component\Form\Extension\Core\Type\EmailType")
     */
    protected $email;

    /**
     * @var string
     * @Type\Field(type="Symfony\Component\Form\Extension\Core\Type\TextareaType")
     */
    protected $comment;

    /**
     * Get the title of the document.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the title of the document.
     *
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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
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
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return string
     */
    public function getDescriptor()
    {
        if ($this->title) {
            return $this->title;
        }

        if (\strlen($this->comment) > 60) {
            return substr($this->comment, 0, 60).'...';
        }

        return $this->comment;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getDescriptor();
    }
}
