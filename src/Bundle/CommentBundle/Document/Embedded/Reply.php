<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\CommentBundle\Document\Embedded;

use Integrated\Bundle\ContentBundle\Document\Content\Relation\Person;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class Reply
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
     * @var string
     */
    protected $text;

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
}
