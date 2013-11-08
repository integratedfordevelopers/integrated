<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\MongoDB\Content\Document\Relation;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Integrated\MongoDB\Content\Document\File;
use Integrated\MongoDB\Content\Document\Embedded\Job;
use Integrated\Common\ContentType\Mapping\Annotations as Content;

/**
 * Document type Relation\Person
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 * @ODM\Document(collection="content")
 * @Content\Document("Person")
 */
class Person extends AbstractRelation
{
    /**
     * @var string
     * @ODM\String
     */
    protected $sex;

    /**
     * @var string
     * @ODM\String
     * @content\Field(label="Title")
     */
    protected $title;

    /**
     * @var string
     * @ODM\String
     * @Content\Field(label="Nickname")
     */
    protected $nickname;
    /**
     * @var string
     * @ODM\String
     * @Content\Field(label="Surname")
     */
    protected $surname;

    /**
     * @var Job[]
     * @ODM\EmbedMany(targetDocument="Integrated\MongoDB\Content\Document\Embedded\Job", strategy="set")
     */
    protected $jobs = array();

    /**
     * @var File
     * @ODM\ReferenceOne(targetDocument="Integrated\MongoDB\Content\Document\File")
     */
    protected $picture;

    /**
     * Get the sex of the document (nothing more!)
     *
     * @return string
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * Set the sex of the document
     *
     * @param string $sex
     * @return $this
     */
    public function setSex($sex)
    {
        $this->sex = $sex;
        return $this;
    }

    /**
     * Get the nickname of the document
     *
     * @return string
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * Set the nickname of the document
     *
     * @param string $nickname
     * @return $this
     */
    public function setNickname($nickname)
    {
        $this->nickname = $nickname;
        return $this;
    }

    /**
     * Get the surname of the document
     *
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set the surname of the document
     *
     * @param string $surname
     * @return $this
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
        return $this;
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
     * Get the jobs of the document
     *
     * @return Job[]
     */
    public function getJobs()
    {
        return $this->jobs;
    }

    /**
     * Set the jobs of the document
     *
     * @param array $jobs
     * @return $this
     */
    public function setJobs(array $jobs)
    {
        $this->jobs = $jobs;
        return $this;
    }

    /**
     * Get the picture of the document
     *
     * @return File
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * Set the picture of the document
     *
     * @param File $picture
     * @return $this
     */
    public function setPicture(File $picture)
    {
        $this->picture = $picture;
        return $this;
    }
}