<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\CommentBundle\Document;

use Doctrine\Common\Collections\ArrayCollection;

use Integrated\Bundle\CommentBundle\Document\Embedded\Reply;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\ContentBundle\Document\Content\Relation\Person;

/**
 * Class Comment
 */
class Comment
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var Person
     */
    protected $author;

    /**
     * @var \DateTime
     */
    protected $date;

    /**
     * @var Content
     */
    protected $content;

    /**
     * @var string
     */
    protected $field;

    /**
     * @var string
     */
    protected $text;

    /**
     * @var ArrayCollection
     */
    protected $replies;

    /**
     * Comment constructor.
     */
    public function __construct()
    {
        $this->date = new \DateTime();
        $this->replies = new ArrayCollection();
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
    public function getReplies()
    {
        return $this->replies;
    }

    /**
     * @param ArrayCollection $replies
     */
    public function setReplies($replies)
    {
        $this->replies = $replies;
    }

    /**
     * @param Reply $reply
     */
    public function addReply(Reply $reply)
    {
        if (!$this->replies->contains($reply)) {
            $this->replies->add($reply);
        }
    }

    /**
     * @param $replyId
     * @return Reply|null
     */
    public function getReplyById($replyId)
    {
        return $this->replies->filter(
                function(Reply $reply) use ($replyId) {
                    return $reply->getId() === $replyId;
                }
            )->first();
    }

    /**
     * @param $replyId
     * @return bool
     */
    public function removeReplyById($replyId)
    {
        if ($reply = $this->getReplyById($replyId)) {
            return $this->replies->removeElement($reply);
        }

        return false;
    }

    /**
     * @return Person
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param Person|null $author
     */
    public function setAuthor(Person $author = null)
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
