<?php

namespace Integrated\Bundle\CommentBundle\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Integrated\Bundle\CommentBundle\Document\Embedded\Author;
use Integrated\Bundle\ContentBundle\Document\Content\Content;

/**
 * Class Comment
 *
 * @ODM\Document
 */
class Comment
{
    /**
     * @var string
     * @ODM\Id(strategy="UUID")
     */
    protected $id;

    /**
     * @var Author
     * @ODM\EmbedOne(targetDocument="Integrated\Bundle\CommentBundle\Document\Embedded\Author")
     */
    protected $author;

    /**
     * @var \DateTime
     * @ODM\Date()
     */
    protected $date;

    /**
     * @var Content
     * @ODM\EmbedOne(targetDocument="Integrated\Bundle\ContentBundle\Document\Content\Content")
     */
    protected $content;

    /**
     * @var string
     * @ODM\String
     */
    protected $field;

    /**
     * @var string
     * @ODM\String
     */
    protected $text;

    /**
     * @var ArrayCollection
     * @ODM\ReferenceMany(targetDocument="Integrated\Bundle\CommentBundle\Document\Comment")
     */
    protected $children;

    /**
     * Comment constructor.
     */
    public function __construct()
    {
        $this->date = new \DateTime();
        $this->children = new ArrayCollection();
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
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return Content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param Content $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return ArrayCollection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param ArrayCollection $children
     */
    public function setChildren($children)
    {
        $this->children = $children;
    }

    /**
     * @param Comment $child
     */
    public function addChildren(Comment $child)
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
        }
    }

    /**
     * @return Author
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param Author $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param string $field
     */
    public function setField($field)
    {
        $this->field = $field;
    }
}
